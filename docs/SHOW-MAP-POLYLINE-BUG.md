# Bug: Polilínea de ruta se desencaja al hacer zoom con leaflet-rotate

## Descripción

En la vista `show.blade.php`, el modal "Cómo llegar" (`geoModal()`) muestra una ruta en
polilínea roja entre la posición del usuario y el lugar. Cuando el mapa tiene `touchRotate: true`
activado (rotación con dos dedos) y el usuario hace pinch-zoom mientras el mapa está rotado,
la polilínea SVG queda desencajada del mapa de tiles.

## Contexto técnico

- **Plugin**: `leaflet-rotate` (incluido vía `resources/js/leaflet.js`)
- **Archivo afectado**: `resources/views/puntos/show.blade.php` → función `geoModal()`
- **Elemento HTML**: `<div id="mapa-geo">`
- **Opciones del mapa**: `rotate: true, touchRotate: true, bearing: 0`

## Causa raíz (hipótesis)

leaflet-rotate posiciona el contenedor SVG (`<svg>`) con un CSS `transform` que combina
traslación + rotación. Cuando el gesto de pinch-zoom cambia simultáneamente el zoom level
Y el bearing (ya que `touchRotate` hace ambas cosas a la vez), el renderer SVG de Leaflet
actualiza los paths internos (`<path d="...">`) pero NO actualiza la posición/tamaño del
contenedor SVG. Resultado: los paths están bien calculados pero el `<svg>` contenedor está
desplazado.

La condición relevante en Leaflet SVG renderer:
```js
_update() {
    if (this._map._animatingZoom && this._bounds) { return; } // salta el update
    // ...
}
```
Durante un gesto que cambia zoom + bearing a la vez, el renderer puede saltar su `_update()`
y el contenedor queda mal posicionado incluso después de que termina la animación.

## Soluciones intentadas (todas fallaron)

### 1. `redraw()` en `zoomend` / `rotateend`
```js
this.mapa.on('zoomend rotateend', () => {
    if (self.rutaLinea) self.rutaLinea.redraw();
});
```
**Por qué falla**: `redraw()` repinta los paths SVG pero no repositiona el contenedor SVG.
Si el `<svg>` está mal posicionado, los paths quedan desencajados aunque estén bien calculados.

### 2. Recrear la polilínea en `zoomend` / `rotateend`
```js
this.mapa.on('zoomend rotateend', () => {
    self.mapa.removeLayer(self.rutaLinea);
    self.rutaLinea = L.polyline(self._rutaCoords, self._rutaOpts).addTo(self.mapa);
});
```
**Por qué falla**: La nueva polilínea se agrega al mismo renderer SVG con el mismo contenedor
mal posicionado. Crear un nuevo `L.polyline` no crea un nuevo renderer.

### 3. Canvas renderer (`L.canvas()`)
```js
const canvasRenderer = L.canvas({ padding: 0.5 });
self._rutaOpts = { color: '#fc5648', weight: 5, opacity: 0.75, renderer: canvasRenderer };
self.rutaLinea = L.polyline(self._rutaCoords, self._rutaOpts).addTo(self.mapa);
```
**Por qué falla**: No se determinó con certeza. Canvas redibuja en cada `moveend` usando
`map.latLngToLayerPoint()`, pero en leaflet-rotate esa función puede devolver coordenadas
incorrectas cuando el mapa está rotado, ya que el canvas también vive dentro del contenedor
rotado y puede tener el mismo problema de transform combinado.

## Soluciones pendientes de investigar

### Opción A — Forzar `_reset()` del renderer (más prometedora)
```js
this.mapa.on('zoomend rotateend', () => {
    // _reset() llama a _update() (repositiona el SVG) y luego redibuja todos los paths
    const renderer = self.rutaLinea?._renderer || self.mapa._renderer;
    if (renderer?._reset) renderer._reset();
});
```
Usa API privada de Leaflet. Puede romperse en actualizaciones.

### Opción B — Forzar `_renderer._update()` con setTimeout
```js
this.mapa.on('zoomend rotateend', () => {
    setTimeout(() => {
        if (self.mapa._renderer?._update) self.mapa._renderer._update();
        if (self.rutaLinea?._reset) self.rutaLinea._reset();
    }, 50);
});
```
El delay de 50ms asegura que `_animatingZoom` ya sea `false` cuando se llama.

### Opción C — Escuchar evento `rotate` directamente
leaflet-rotate dispara eventos `rotate` y `bearing` además de `zoomend`. Puede que el
evento correcto sea uno de esos y no `rotateend`.

### Opción D — Eliminar `touchRotate`, agregar botón "Norte arriba"
```js
// Sin touchRotate
this.mapa = L.map('mapa-geo', { rotate: true, bearing: 0, ... });

// Botón para reorientar al norte
<button onclick="resetBearing()">⬆ Norte</button>
<script>function resetBearing() { self.mapa.setBearing(0); }</script>
```
Elimina el bug completamente. El usuario puede rotar el mapa via botón en pasos de 45°
en lugar de gesto libre. En un modal de navegación (llegar a un lugar), norte-arriba
es más útil que libre rotación.

### Opción E — Usar Mapbox GL JS en lugar de Leaflet
Mapbox GL renderiza en WebGL y tiene rotación nativa sin los problemas de SVG.
Cambio significativo: diferente API, diferente licencia, diferente bundle size.

### Opción F — GeoJSON layer en vez de L.polyline
```js
self.rutaGeoJson = L.geoJSON({
    type: 'Feature',
    geometry: { type: 'LineString', coordinates: rutaCoords.map(c => [c[1], c[0]]) }
}, { style: { color: '#fc5648', weight: 5 } }).addTo(self.mapa);
```
GeoJSON usa el mismo renderer SVG internamente, probablemente mismo bug.

## Estado actual del código (`show.blade.php`)

La función `geoModal()` está en el estado original del commit `ab3eae9`:
- `rotate: true` (sin `touchRotate`)
- Marcador de usuario: círculo azul simple (sin flecha de brújula)
- `setBearing(heading)` activo desde `pos.coords.heading` (causa movimiento entrecortado)
- Sin listener de `DeviceOrientationEvent`

Los fixes aplicados en esta sesión (brújula sin/cos, canvas renderer, etc.) se perdieron
porque el IDE sobrescribió el archivo. Ver historial de conversación para recuperarlos.

## Fixes aplicados pero perdidos (re-aplicar)

Estos cambios estaban bien y no causaban el bug — se deben re-aplicar:

1. **Agregar `touchRotate: true`** al mapa
2. **Reemplazar `setBearing(heading)`** — eliminarlo, no auto-rotar el mapa
3. **Reemplazar marcador azul** con marcador compuesto (pulse ring + cono + dot) usando CSS de `index_puntos.blade.php`
4. **Agregar `iniciarBrujula()`** con `DeviceOrientationEvent`, fórmula Android `(360 - alpha - 30 + 720) % 360`, filtro sin/cos EMA factor 0.15, dead zone 5°
5. **Agregar `detenerBrujula()`** en `cerrar()` para limpiar el listener
6. **Fix `panTo` vs `fitBounds`**: no llamar `panTo` en el primer fix (solo `fitBounds`)
7. **Agregar CSS** de `.gps-marker`, `.gps-cone-wrap` etc. al `@section('head')` de `show.blade.php`
