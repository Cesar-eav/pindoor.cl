# Ticketera — reservas con cupo y pago Flow

## Qué es

La ticketera permite a un operador turístico vender tours con horario y cupo limitado,
pagados online vía [Flow](https://www.flow.cl/) (Webpay, tarjetas, etc.). El precio,
los horarios y las fechas bloqueadas **no son propiedad de la ruta** — son propiedad del
**par (ruta, operador)**, configurado en la tabla pivote `ruta_operador_turistico` desde
`Admin/OperadorRutaController`. Es fácil asumir que el precio pertenece a la `Ruta`; en
realidad dos operadores pueden vender el mismo tour con precios, horarios y fechas
bloqueadas totalmente independientes.

## El flujo completo

1. El visitante entra a `reservar.blade.php`, elige una fecha en el calendario
   (`x-calendario-disponibilidad`) y un horario disponible para esa fecha.
2. `ReservaController::store()` valida los datos, dentro de una `DB::transaction()`
   toma un lock sobre el horario y sobre las reservas competidoras, calcula el precio
   server-side (`RutaOperador::calcularPrecio()`), crea la `ReservaRuta` en estado
   `pendiente` con `expira_en = now()->addMinutes(30)`, y redirige a la pasarela de Flow.
3. El pago ocurre en el sitio de Flow, fuera de la app.
4. Dos caminos de confirmación corren en paralelo, sin coordinarse entre sí:
   - **Webhook** `FlowWebhookController::confirmar()` — la fuente de verdad.
   - **Retorno del navegador** `FlowRetornoController::show()` — respaldo: si la
     reserva sigue `pendiente`, vuelve a consultar Flow y aplica el resultado, pero si
     esa consulta falla igual muestra la página con el último estado conocido en BD
     (el webhook es quien manda).
   - Ambos convergen en el único punto de transición de estado:
     `ReservaRuta::aplicarEstadoFlow()`, que toma `lockForUpdate()` sobre la reserva
     justamente porque webhook y retorno pueden llegar casi al mismo tiempo, y solo
     dispara las notificaciones de pago/rechazo si el estado realmente cambió — así
     un reintento del webhook de Flow no reenvía el correo dos veces.
5. `ExpirarReservasPendientes` corre cada 5 minutos (`app/Console/Kernel.php`): primero
   vuelve a consultar Flow para toda reserva `pendiente` con `flow_token` (por si el
   webhook se perdió y el cliente cerró la pestaña antes del retorno), y **recién
   después** expira en bloque lo que sigue `pendiente` y ya venció.
6. Acciones de admin sobre una reserva ya pagada — `reembolsar()` y `reagendar()` en
   `ReservaRuta` — siguen el mismo patrón de transacción + lock, y dejan un registro en
   `ReservaGestion`.

## Cómo se evita la sobreventa

Patrón de doble lock, repetido en `ReservaController::store()` y `ReservaRuta::reagendar()`:

1. `RutaOperadorHorario::lockForUpdate()` sobre la fila del horario. Ojo: un horario
   recurrente semanal es **una sola fila** compartida por todas las fechas en que aplica,
   así que este lock serializa reservas concurrentes de ese horario **para cualquier
   fecha**, no solo la fecha que se está reservando — un trade-off de escalabilidad
   consciente, no un descuido.
2. Dentro de la misma transacción, se vuelve a consultar y lockear las reservas
   competidoras de ese horario+fecha (`pagada`, o `pendiente` sin vencer todavía), se
   suma `adultos + niños`, y se rechaza si supera `cupo_maximo`.

**No hay constraint de base de datos que garantice esto** — es 100% control a nivel
aplicación. Cualquier código nuevo que cambie estado o cantidades de una reserva sin
seguir este mismo orden (lock horario → lock+suma reservas → validar cupo) reintroduce
la condición de carrera en silencio, sin que salte ningún error hasta que dos reservas
choquen en producción.

Las consultas de disponibilidad para mostrar cupo en pantalla (`cupoOcupado()` /
`cupoDisponible()`) **no** toman lock — son de solo lectura para la UI; el cupo real se
valida recién al confirmar el `store()`.

## Precio individual vs. grupo

Regla exacta (`RutaOperador::calcularPrecio()`): **1 adulto y 0 niños** → tarifa flat
individual (`precio_individual`). Cualquier otra combinación → `adultos *
precio_grupo_adulto + niños * precio_nino`.

El caso que confunde: **1 adulto + 1 niño (2 personas) NO es tarifa individual** — cae
en la fórmula de grupo, aunque solo haya un adulto. La tarifa individual exige cero
acompañantes de cualquier tipo, no solo cero adultos adicionales.

El precio se calcula **exclusivamente server-side** a partir de las cantidades ya
validadas — el cliente nunca puede enviar un precio en el request. El cálculo
equivalente en Alpine.js (`reservar.blade.php`) es solo para mostrar el "Total
estimado" antes de pagar; no tiene ninguna autoridad.

## Credenciales de Flow configurables desde el admin

`FlowService` lee `apiKey`/`secretKey`/`modo` desde la tabla `configuraciones` (modelo
genérico `Configuracion`, clave/valor), y si esa clave está vacía cae de nuevo a
`config('services.flow.*')` (`.env`: `FLOW_API_KEY`, `FLOW_SECRET_KEY`, `FLOW_SANDBOX`).
Es decir, la BD siempre gana si tiene un valor cargado — el `.env` es solo el bootstrap
inicial.

Esta capa se agregó (commit "Credenciales FLow desde Admin") para que el dueño de la
cuenta pudiera rotar claves o cambiar entre sandbox y producción desde
`Admin/ConfiguracionController` sin pasar por un deploy. El formulario usa el patrón
"dejar el campo vacío = no cambiar la clave existente" y nunca reimprime el secreto en
el HTML.

**Ojo:** las claves quedan guardadas **en texto plano** en `configuraciones` — no hay
cast `encrypted` en el modelo. El enmascarado es solo a nivel de formulario (evita que
se vea en el navegador); no protege si alguien tiene acceso directo a la base de datos
o si a futuro se agrega alguna vista que liste `Configuracion::all()`.

## `RutaOperadorBloqueo` y `ReservaGestion`

- **`RutaOperadorBloqueo`**: fechas puntuales bloqueadas por operador+ruta (feriados,
  mantenciones). Bloquea el **día completo para todos los horarios** de ese
  operador+ruta — no existe bloqueo por horario individual (no se puede cerrar solo el
  tour de las 10am dejando activo el de las 3pm del mismo día). Se consulta vía
  `RutaOperador::fechaBloqueada()`, que revisa la relación `bloqueos` ya cargada en
  memoria — quien la use debe hacer `->load('bloqueos')` antes, como hace
  `ReservaController` en cada punto donde valida disponibilidad.
- **`ReservaGestion`**: log de auditoría *append-only* (`reembolso` / `reagendamiento` /
  `nota`) sin efecto propio en la lógica de negocio — es solo el registro de qué pasó y
  cuándo. El cambio de estado real ocurre en la misma transacción que el registro de
  gestión, así que nunca quedan desincronizados entre sí.

## Gotchas y decisiones no obvias

- **Zona horaria implícita**: todas las comparaciones de fecha (`aplicaEnFecha`,
  `fechaBloqueada`, cupo, calendario) asumen `America/Santiago` (config de la app) sin
  ningún manejo explícito de UTC ni DST. Bajo riesgo para Chile hoy, pero es una
  asunción implícita, no algo verificado con tests.
- **Doble chequeo de "no fechas pasadas"**: el servidor clampa el calendario mensual a
  `hoy` en `disponibilidadMes()`, y por separado el componente Alpine deshabilita el
  botón de mes anterior y marca los días pasados en el cliente. `store()` vuelve a
  validar igual, así que no es explotable — pero son dos lugares con la misma regla que
  pueden desincronizarse si se toca solo uno.
- **`expira_en = now()->addMinutes(30)` está hardcodeado y duplicado** en
  `ReservaController` y `Admin/PagoPruebaController` — no es config ni constante de
  modelo. Cambiar la ventana de reserva requiere tocar los dos lugares.
- **`RutaOperador extends Pivot`**: el trait `AsPivot` de Laravel pisa
  `getForeignKey()` para su propio uso interno, así que la convención automática de
  Eloquent para `hasMany`/`hasManyThrough` no sirve en este modelo — cada relación
  declara su foreign key a mano. Hay un comentario explícito en el código sobre esto;
  vale la pena leerlo antes de agregar una relación nueva ahí.
- **El enum `estado` de `ReservaRuta` creció con `ALTER TABLE ... MODIFY` crudo** (para
  agregar `reembolsada`), específico de MySQL y no trivialmente reversible. Agregar un
  estado nuevo a futuro implica el mismo tipo de migración cruda, no solo un cambio de
  constante en PHP.
- **Horarios/operadores con reservas existentes se desactivan en vez de borrarse**
  (`activo=false` / `ticketing_activo=false`) para no dejar huérfano el historial de
  reservas ya hechas.

## Puntos frágiles / riesgos conocidos

- **`Admin/PagoPruebaController::store()` crea reservas de prueba sin transacción, sin
  lock y sin chequeo de cupo**, y esas reservas de prueba sí cuentan para el cupo real
  (la consulta de cupo no filtra `es_prueba`). Un admin generando pagos de prueba contra
  una ruta casi llena puede sobrevender un cupo real sin que nada lo impida. Es el
  hallazgo más concreto tipo "bug latente" de todo este sistema.
- **Sin retry ni backoff en las llamadas HTTP a Flow** (`FlowService`, timeout 15s) —
  un problema transitorio de red anula la reserva del cliente en vez de reintentar, y el
  cliente pierde su cupo reservado y debe empezar de nuevo.
- **`ExpirarReservasPendientes` re-consulta Flow secuencialmente, reserva por reserva,
  sin batching ni límite** — a mayor volumen de reservas pendientes simultáneas, el
  comando puede alargarse o chocar con el rate limit de Flow, sin ninguna protección
  para ese escenario más allá de un try/catch por ítem.
- **No hay tests automatizados** para nada de esta lógica (orden de locks, cálculo de
  precio, máquina de estados, idempotencia de notificaciones, reembolso/reagendamiento).
  Dado lo sutil que es el orden de locking y las condiciones de carrera que previene, es
  el área con más riesgo de regresión silenciosa si se toca sin cuidado.
