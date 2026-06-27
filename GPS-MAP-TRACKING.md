# GPS Map Tracking — Diagnóstico y Mejoras

## Archivos involucrados
- `resources/views/puntos/partials/_scripts.blade.php` — lógica JS del mapa y GPS
- `resources/views/puntos/index_puntos.blade.php` — HTML del mapa + CSS del marcador GPS

---

## Estado actual del código (resumen)

```js
// Variables de estado
let marcadorUbicacion   = null;   // L.marker del usuario
let watchIdMapa         = null;   // ID del watchPosition activo
let lastBearing         = null;   // último heading visual registrado
let siguiendo           = false;  // follow mode activo
let btnRecenterRef      = null;   // referencia al botón recentrar
let orientationListener = null;   // { tipo, handler } del DeviceOrientationEvent
let lastOrientationTs   = 0;      // timestamp último evento brújula (throttle 20fps)
let _smoothSin          = null;   // componente sin del heading filtrado
let _smoothCos          = null;   // componente cos del heading filtrado

// toggleUbicacion(btn) — 3 estados:
// 1. GPS off → encender (watchPosition, flyTo, crear marcador + iniciarBrujula())
// 2. GPS on + siguiendo → apagar GPS + detenerBrujula()
// 3. GPS on + no siguiendo (usuario arrastró) → recentrar y retomar
```

**Cómo funciona la flecha de dirección:**
- Fuente: `DeviceOrientationEvent` (`deviceorientationabsolute` en Android, `webkitCompassHeading` en iOS)
- **El mapa NO rota automáticamente** — solo la flecha indica la dirección
- El usuario puede rotar el mapa manualmente con dos dedos (`touchRotate: true`)
- Fórmula Android: `(360 - alpha - 30 + 720) % 360` — negar alpha corrige espejo E-W, offset calibra Norte
- Filtro sin/cos: suaviza por separado sin y cos para evitar el giro brusco cerca de 0°/360°
- Factor de suavizado: `0.15` (más estable que `0.3` anterior)
- Dead zone: `5°` (suficiente para absorber ruido del magnetómetro)

**Cómo funciona el seguimiento de posición:**
- `panTo([lat, lng], { animate: true, duration: 0.8 })` al llegar cada fix GPS
- Guard `!mapaLeaflet._animatingZoom` evita interrumpir el `flyTo` inicial
- **Sin CSS transition en `._icon`**: se eliminó porque cualquier pan/zoom/rotación del mapa la activaba, desencajando visualmente el marcador
- El marcador salta directo a la nueva posición GPS (los fixes son poco frecuentes, el salto es imperceptible)

---

## Decisiones de diseño importantes

### El mapa no rota con la brújula
Google Maps no rota el mapa al caminar — solo muestra la flecha de dirección en el marcador. Auto-rotar el mapa causaba que `setBearing()` (20fps) compitiera con `panTo()` de GPS, generando movimiento entrecortado. Ahora el mapa es estable; el usuario puede rotarlo manualmente.

### Sin CSS transition en `._icon`
La transición `transform 0.8s` en el elemento `._icon` de Leaflet afecta a CUALQUIER cambio de transform, incluidos los causados por zoom, pan y rotación del mapa. Esto hacía que el marcador se "deslizara" visualmente al interactuar con el mapa. La solución es no usar CSS transition — el marcador salta instantáneamente a la posición GPS.

### Filtro sin/cos en lugar de promedio directo de ángulos
Promediar ángulos directamente causa el "giro loco" cerca de 0°/360°: si el sensor oscila entre 358° y 2°, el promedio da 180° (sur). Suavizando sin y cos por separado y reconstruyendo con `atan2`, el promedio de 358° y 2° da correctamente 0° (norte).

### rAF loop de seguimiento — descartado
Se intentó un loop `requestAnimationFrame` que interpolaba posición y bearing a 60fps con `panTo(animate: false)`. Causó dos problemas: (1) las llamadas a `panTo` a 60fps rompían la carga de tiles (segmentos grises), (2) el loop interfería con los gestos táctiles de zoom y rotación. Revertido a `panTo` simple.

---

## Bugs resueltos

### BUG-1 ✅ Cono no reaparece al reanudar movimiento
`lastBearing = null` al ocultar el cono → en el próximo fix, el delta es ∞ → cono reaparece.

### BUG-2 ✅ Wrap-around 0°/360°
`delta = Math.min(rawDelta, 360 - rawDelta)` — aritmética circular correcta.

### BUG-3 ✅ `panTo` interrumpe `flyTo`
Guard `!mapaLeaflet._animatingZoom` antes de `panTo`.

### BUG-4 ✅ `moveend` se dispara por drag del usuario
Reemplazado por `setTimeout(1100ms)` tras el `flyTo`.

### BUG-5 ✅ Sin feedback al adquirir señal GPS
Spinner en el botón durante adquisición; ícono original restaura al primer fix.

### BUG-7 ✅ En Android WebView, `heading` GPS siempre null
`DeviceOrientationEvent` (`deviceorientationabsolute`) como fuente principal — funciona incluso en quieto.

### BUG-8 (evaluado) Auto-start GPS pide permiso en desktop
En mobile es el comportamiento deseado. En desktop el popup puede sorprender, pero es aceptable.

### BUG-9 ✅ Flecha "loca" cerca del norte (0°/360°)
Filtro sin/cos en lugar de EMA directa sobre ángulos. El giro brusco al cruzar 0° desaparece.

### BUG-10 ✅ Flecha con espejo Este-Oeste en Android
`deviceorientationabsolute.alpha` en Android Chrome es antihorario (convención W3C). Negarlo con `(360 - alpha + offset)` corrige el espejo. Offset de calibración: `-30`.

### BUG-11 ✅ Marcador se desencaja al rotar/hacer zoom con dedos
CSS transition en `._icon` se activaba con cualquier cambio de transform del mapa. Eliminada por completo.

---

## Mejoras de UX pendientes

### UX-1: Indicador de precisión GPS en el pulso
Alta precisión = pulso pequeño y rápido. Baja precisión = pulso grande y lento. Ajustable via JS según `pos.coords.accuracy`.

---

## Historial de iteraciones

### Iteración 1 — Animación inicial
- `rotate: true, bearing: 0` en Leaflet + `setBearing(heading)` al cambiar dirección
- Marcador GPS con `L.divIcon` + SVG flecha roja

### Iteración 2 — Centrado y seguimiento
- `panTo` al mover el marcador GPS
- Umbral 10° para rotar el mapa
- Variable `lastBearing`

### Iteración 3 — rAF + círculo de precisión (revertido)
- `requestAnimationFrame` para interpolación 60fps + `L.circle` para precisión
- **Problema:** rAF y `panTo` competían → desync visual
- **Revertido**

### Iteración 4 — CSS transition
- CSS `transition: transform 0.8s linear` en `._icon`
- Marcador compuesto (pulse ring + cono + dot) en un único `divIcon`
- Follow mode + botón Recentrar

### Iteración 5 — Bug fixes
- BUG-1 al BUG-5 corregidos
- `touchRotate: true` — dos dedos para rotar mapa
- Capa de tiles única `voyager` (labels ya no se desencajan)

### Iteración 6 — Brújula DeviceOrientationEvent
- `iniciarBrujula()` / `detenerBrujula()` con `deviceorientationabsolute`
- Flecha funciona en quieto (sin necesidad de estar caminando)

### Iteración 7 — Estabilización y corrección de brújula (estado actual)
- **Mapa no rota** con brújula — solo la flecha (como Google Maps)
- **CSS transition eliminada** de `._icon` — fixes desencaje en zoom/rotación/pan
- **Filtro sin/cos** — elimina giro brusco cerca de 0°/360° (BUG-9)
- **Fórmula Android corregida**: `(360 - alpha - 30 + 720) % 360` — corrige espejo E-W (BUG-10)
- **Dead zone 5°** en lugar de 1° — absorbe ruido del magnetómetro
- **Factor suavizado 0.15** en lugar de 0.3 — más estable en quieto
- rAF loop de seguimiento descartado — rompía tiles y gestos táctiles
