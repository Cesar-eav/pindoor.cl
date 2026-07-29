# Bug: eventos de agenda de clientes desaparecían de /panoramas

## Síntoma

Un cliente crea un evento desde la agenda de su ficha (módulo `eventos`, tabla
`punto_modulo_items`). El evento cumple todas las condiciones para mostrarse
(`activo=1`, fecha dentro de la ventana configurada, negocio `activo`/no `eliminado`),
pero no aparece en el listado público `/panoramas` ni en `/admin/panoramas`.

El patrón engañaba: de varios eventos de clientes en la misma fecha, solo **uno**
sobrevivía — sin relación aparente con el negocio, la categoría o la fecha. Eso llevó
a sospechar primero de filtros normales (ventana de días, estado del punto), que
resultaron ser rojo herring.

## Causa raíz

En `PuntoInteresController::panoramas()` (y su equivalente en
`Admin/PanoramaController::index()`), los eventos de `ModuloItem` se convierten en
instancias `Panorama` de solo lectura para reusar toda la lógica de agrupación/
renderizado de la vista:

```php
$fake = new Panorama();
$fake->fill([...]);
$fake->setAttribute('id', 'ev_' . $item->id);   // string no numérico
$fake->setRelation('imagenes', collect());
```

Dos comportamientos de Eloquent se combinan para romper esto:

1. **`Model::getCasts()` castea automáticamente la primary key a su `$keyType`**
   (`Concerns/HasAttributes.php`): `array_merge([$this->getKeyName() => $this->getKeyType()], $this->casts)`.
   Como `Panorama` no declara `$keyType`, el default es `'int'`. Resultado:
   `$panorama->id` — aunque en `$attributes` esté guardado el string `"ev_19"` —
   siempre se lee como `(int) "ev_19"` = **`0`**. Todos los eventos de clientes
   terminan con `id === 0`, sin importar cuál `ModuloItem` los originó.

2. **`Illuminate\Database\Eloquent\Collection::merge()` no es un merge posicional**:
   arma un diccionario por `getKey()` y sobreescribe entradas con la misma clave
   (`Collection.php`, método `merge()`). Como los tres/cuatro eventos de clientes de
   un mismo request comparten `getKey() === 0`, cada uno pisa al anterior — solo
   sobrevive el último que procesa la query (orden no determinístico, sin `ORDER BY`
   explícito).

Ninguna de las dos partes es un bug de Laravel: es el costo de usar una instancia real
de Eloquent (`new Panorama()`) como si fuera un DTO liviano. El id sintético
`'ev_' . $item->id` nunca debió pasar por `setAttribute('id', ...)` de un modelo con
`$keyType = 'int'`.

## Por qué costó tanto encontrarlo

- **Sin excepción, sin log.** El cast a `0` y el pisado en el diccionario son
  comportamiento "normal" de Eloquent — no hay error que apunte al lugar correcto.
- **El síntoma parecía depender del negocio/fecha**, cuando en realidad dependía del
  orden de iteración de una query sin `ORDER BY`. Esto hizo perder tiempo revisando
  `activo`/`eliminado` del punto, que nunca fue la causa.
- El entorno de depuración (este repo vía Claude Code) no estaba conectado a la base
  de datos real de producción — varias verificaciones iniciales se hicieron contra una
  copia desactualizada, hasta confirmar con `tinker` corrido directamente en el
  servidor (`cpi116106@int15`).

## El fix

Cambiar `merge()` por `concat()` en ambos controladores — `concat()` concatena sin
usar ninguna clave como índice de deduplicación:

- `app/Http/Controllers/PuntoInteresController.php` → `panoramas()`
- `app/Http/Controllers/Admin/PanoramaController.php` → `index()`

```php
// antes
$panoramas = $panoramas->merge($eventosCliente)->sortBy(...)->values();

// después
$panoramas = $panoramas->concat($eventosCliente)->sortBy(...)->values();
```

## Lección para el futuro

Si se necesita un objeto "de mentira" para reusar una vista pensada para un modelo
Eloquent, **no usar `new Modelo()` + `setAttribute('id', ...)`** con un id no numérico.
Alternativas más seguras:
- Un objeto simple (`stdClass`, array, o una clase liviana propia) con los mismos
  campos que la vista necesita — sin heredar el comportamiento de Eloquent.
- Si de todas formas se usa el modelo real, evitar `Collection::merge()` para
  combinarlo con otra colección de modelos — usar `concat()`, que no depende del
  `id`.
