# Estrategia: Mapa in-app con Leaflet

Rama: `feature/mapa-in-app`

---

## Situación actual

La ficha de un lugar (`/lugar/{slug}`) tiene **tres puntos de salida** hacia Google Maps:

| # | Ubicación | Quién lo ve |
|---|-----------|-------------|
| 1 | Sidebar derecho desktop — botón "Ir al mapa" (`line ~393`) | Desktop |
| 2 | Sección "Cómo llegar" — mini-mapa Leaflet + enlace "Abrir en Google Maps" (`line ~1301`) | Desktop + móvil |
| 3 | FAB flotante "Mapa" fijo en la parte inferior (`line ~1405`) | Móvil |

Además, existe ya un **mini-mapa Leaflet** (`#mini-mapa`) funcional en la sección "Cómo llegar".  
Leaflet ya está instalado (`"leaflet": "^1.9.4"`) y hay un bundle `resources/js/leaflet.js` compilado con Vite.

El **mapa del explorador** (`/`) ya usa Leaflet con `_scripts.blade.php` y muestra todos los puntos.

---

## Objetivo

Reemplazar los tres enlaces a Google Maps por una experiencia **in-app** sin salir de Pindoor.

---

## Estrategia por punto de salida

### Punto 1 — Botón "Ir al mapa" (desktop sidebar)

**Acción:** Convertirlo en un botón Alpine que cambie `vista = 'como_llegar'`.  
El panel "Cómo llegar" ya existe y contiene el mini-mapa. El botón del sidebar solo necesita activar esa vista.

```blade
{{-- antes: enlace externo --}}
<a href="https://www.google.com/maps?q={{ $punto->lat }},{{ $punto->lng }}" target="_blank">
    📍 Ir al mapa
</a>

{{-- después: botón Alpine --}}
<button @click="vista = 'como_llegar'" class="...">
    📍 {{ __('ui.lugar.ir_al_mapa') }}
</button>
```

---

### Punto 2 — "Abrir en Google Maps" bajo el mini-mapa

El mini-mapa Leaflet ya funciona (`#mini-mapa`, zoom 15, tile CartoCDN light).  
Solo hay que **eliminar el enlace externo** y opcionalmente ampliar el mapa a `h-64` o más para que sea más usable.

Podemos agregar al mini-mapa:
- **Popup** al clicar el marcador con el nombre del lugar.
- **Botón "+" / zoom** habilitado (actualmente `zoomControl: false`).
- Un botón secundario discreto "¿Cómo llegar?" que abra navegación nativa del dispositivo vía `geo:` URI (`geo:LAT,LNG`) — esto sí abre el app de mapas del teléfono pero **solo si el usuario lo pide explícitamente**, lo contrario del flujo actual donde el primer toque ya sale.

```html
<!-- URI geo: abre Apple Maps en iOS, Google Maps en Android, sin salir directamente -->
<a href="geo:{{ $punto->lat }},{{ $punto->lng }}?q={{ urlencode($punto->title) }}"
   class="texto-secundario-pequeño">
   Abrir en app de navegación →
</a>
```

---

### Punto 3 — FAB móvil "Mapa"

El FAB actual navega fuera de la app. La alternativa es hacer scroll hasta el panel "Cómo llegar" (que tiene el mini-mapa).

**Acción:** Cambiar el `<a>` externo por un `<button>` que:
1. Activa `vista = 'como_llegar'` en Alpine.
2. Hace scroll suave al panel.

```blade
<button
    @click="vista = 'como_llegar'; $nextTick(() => document.getElementById('panel-como-llegar').scrollIntoView({ behavior: 'smooth' }))"
    class="lg:hidden fixed left-4 z-50 ...">
    📍 Mapa
</button>
```

---

## Mejora opcional: mapa ampliado con modal

Si queremos que el usuario pueda explorar bien el mapa (ver calles, cerros cercanos), se puede mostrar un modal Alpine con un mapa Leaflet más grande (pantalla casi completa).

```html
<div x-data="{ abierto: false }">
    <button @click="abierto = true">Ver en mapa</button>

    <div x-show="abierto" x-transition class="fixed inset-0 z-[9999] bg-black/60 flex items-end">
        <div class="w-full bg-white rounded-t-3xl" style="height: 85dvh">
            <div id="mapa-modal" class="w-full h-full rounded-t-3xl"></div>
        </div>
    </div>
</div>
```

El mapa modal se inicializa solo cuando `abierto` pasa a `true` (evita render innecesario).

---

## Archivos a modificar

| Archivo | Cambios |
|---------|---------|
| `resources/views/puntos/show.blade.php` | Reemplazar los 3 enlaces externos; mejorar mini-mapa |
| `lang/es/ui.php` + `lang/en/ui.php` | Agregar clave `lugar.abrir_navegacion` si se agrega el enlace `geo:` |

Sin nuevos archivos, sin nuevas rutas.

---

## Orden de implementación sugerido

1. **Punto 2** primero — mejorar el mini-mapa existente (ampliar altura, habilitar zoom, popup).  
   Eliminar el enlace "Abrir en Google Maps" o reemplazarlo por el `geo:` link.
2. **Punto 3** — cambiar FAB móvil para activar `vista='como_llegar'` en vez de salir.
3. **Punto 1** — cambiar botón sidebar desktop por trigger Alpine.
4. **(Opcional)** Modal mapa ampliado si se decide necesario tras probar los pasos 1–3.

---

## Qué NO cambia

- El mapa del explorador (`/`) ya es in-app, no se toca.
- El `SelectorMapa.vue` del panel admin no se toca (es para edición de coordenadas).
- Los tiles se sirven desde CartoCDN (sin clave de API, sin costos).
