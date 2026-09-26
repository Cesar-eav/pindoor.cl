# Pindoor — Contexto del proyecto

Este documento describe qué es Pindoor y qué funcionalidades tiene **hoy**, tanto para el visitante turístico como para el negocio cliente. Está pensado como contexto para pensar ideas de mejora — no propone soluciones, solo describe el estado actual.

## Qué es Pindoor

Guía turística digital de Valparaíso, Chile: un sitio web público (app-like) donde cualquier persona puede descubrir lugares, eventos y experiencias de la ciudad, más un panel de gestión donde negocios locales, artistas y operadores turísticos administran su propia información.

## Stack técnico (resumen)

- Laravel 10 / PHP 8.3 / MySQL.
- Blade + Alpine.js + Tailwind CSS v4, con toques de Livewire en algunas secciones (agenda de eventos).
- Progressive Web App (instalable, funciona offline con fallback) + app nativa Android (NativePHP) con login Google.
- Pagos de entradas/reservas vía Flow (pasarela chilena).
- Analítica: Microsoft Clarity + Google Tag Manager, con opción de opt-out.
- Multi-idioma: español, inglés, francés.

## Roles de usuario

- **Turista / visitante**: navega de forma anónima, sin cuenta propia. No existe login para turistas.
- **Cliente**: dueño de un negocio (cafetería, museo, tienda, alojamiento, etc.), gestiona su propia ficha en `/cliente/*`.
- **Artista**: perfil artístico individual o banda, gestiona su ficha y agenda en `/artista/*`.
- **Operador turístico**: gestiona su perfil público y puede vender rutas/experiencias.
- **Admin**: gestión global del sitio en `/admin/*`.

## El modelo central: lugares (`PuntoInteres`)

Cualquier lugar de la app —desde un mirador hasta una cafetería con dueño activo— es un "punto de interés". Se agrupan en categorías, y cada categoría habilita ciertos módulos de contenido:

| Categoría | Módulos que puede usar |
|---|---|
| Miradores, Street Art, Monumentos, Naturaleza, Arquitectura, Estatuas, Ascensores | Sin módulos propios (fichas informativas) |
| Cafeterías, Picadas, Comer | Oferta del día, menú del día, carta |
| Cultura | Oferta del día, agenda de eventos |
| Museos | Oferta del día, entradas, exposiciones |
| Alojar | Oferta del día, habitaciones, servicios, políticas |
| Tiendas, Artesanía | Catálogo de productos |

Todas las categorías con dueño activo también acceden a: oferta del día, avisos y promociones (módulos "transversales").

Un lugar puede estar en dos estados: **"básico"** (ficha creada por el admin, sin dueño reclamado, con info limitada) o **"activado/cliente"** (el dueño lo reclamó o lo creó, con acceso al panel completo).

## Experiencia pública (lo que ve y hace un turista)

### Home (`/`)
- Dos vistas intercambiables: **lista** y **mapa** (Leaflet, con marcadores por categoría y ubicación del usuario).
- Filtros por categoría y búsqueda de texto libre (la búsqueda también encuentra eventos, artistas y operadores, no solo lugares).
- Botón **"Cerca de ti"**: usa el GPS del navegador y reordena los resultados por distancia.
- Sin filtros activos, la home muestra secciones modulares (orden configurable desde admin): carruseles por categoría, mapa general, próximos eventos (mezcla eventos de admin + agenda de clientes + agenda de artistas), últimos posts del blog, últimas rutas, recomendaciones curadas ("Pindoor Recomienda"), negocios destacados y últimas experiencias.
- Buscador con sugerencias instantáneas mientras se escribe.
- Banner de "fase piloto" invitando a dejar feedback.
- Tarjetas promocionales invitando a negocios no clientes a "Publica tu negocio en Pindoor".

### Explorar (`/explorar`)
Listado paginado completo con los mismos filtros que la home, sin el ordenamiento por GPS — es el "ver todos" cuando una categoría o búsqueda tiene muchos resultados.

### Ficha de un lugar (`/lugar/{slug}`)
- SEO completo (JSON-LD, Open Graph).
- Galería de fotos con lightbox/zoom (los lugares "básicos" solo muestran una foto; la galería completa se desbloquea al reclamar el negocio).
- Video de YouTube embebido si el dueño lo cargó.
- Contenido modular según categoría: carta, oferta del día, menú, avisos, promociones, habitaciones/servicios/políticas (alojamiento), entradas (eventos con venta de tickets), exposiciones, agenda.
- Mini-mapa y un modal "Cómo llegar" con ruta caminando desde la ubicación del usuario y deep-link a Google Maps.
- Botón compartir (nativo del sistema o WhatsApp/copiar link), cada share queda registrado para estadísticas del admin.
- Sección "Los más cercanos" (8 lugares cercanos de cualquier categoría).
- Si el lugar es "básico" (sin dueño), muestra un CTA "¿Eres el propietario de este espacio?" que dispara un flujo de reclamo de negocio, revisado por un admin.
- **No hay** reseñas, calificaciones, ni favoritos/wishlist en ninguna parte del sitio.

### Detalle de exposición / evento / producto
Páginas dedicadas por ítem (exposición de museo, evento de agenda, producto de tienda/artesanía), cada una con su propio contenido, imágenes y metadatos SEO.

### Panoramas — agenda de eventos (`/panoramas`)
Agenda día por día que unifica eventos de admin, agenda de clientes y agenda de artistas. Incluye filtros por categoría (incluyendo "gratuito"), navegación por mes, eventos recurrentes (semanales, "tal día del mes"), sección de exposiciones, un archivo de eventos pasados ("Re-vival"), y un popup de suscripción a newsletter.

### Experiencias (`/experiencias`)
Experiencias propuestas por la comunidad o negocios (tours, talleres, etc.), filtrables por categoría/gratuidad. Cualquiera puede proponer una nueva experiencia, que queda pendiente de aprobación del admin.

### La Escena — artistas (`/la-escena`)
Directorio de artistas locales con perfil público (incluye bandas con múltiples integrantes) y su propia agenda de eventos. Los artistas pueden autoregistrarse.

### Operadores turísticos
Perfil público propio; también pueden autoregistrarse.

### Rutas (`/rutas`)
Recorridos curados a pie por la ciudad; algunas son "rutas oficiales" reservables con pago (vía Flow), con calendario de disponibilidad, confirmación por email y gestión de reembolso/reprogramación desde el admin.

### Otras secciones
Blog editorial, páginas de "Pindoor Recomienda" (recomendaciones curadas de un solo lugar), formularios de registro de negocio/artista/operador, contacto general, newsletter, selector de idioma (es/en/fr).

### Capa transversal
PWA instalable con modo offline, navegación inferior móvil con FAB expandible (Experiencias, Contacto, La Escena, Instagram, Ingresar/Registrarme), analítica con opt-out.

### Funcionalidades que hoy NO existen
- Reseñas o calificaciones de lugares.
- Favoritos / lista de deseos.
- Cuentas de usuario para turistas (todo es anónimo).
- Gamificación, puntos de fidelidad o códigos QR.
- Estadísticas de visitas visibles para el negocio cliente (solo el admin las ve).

## Panel de gestión — Cliente (`/cliente/*`)

- **Alta de negocio**: autoservicio, con ubicación por mapa (geocodificación), contacto WhatsApp y una foto inicial; puede requerir aprobación de un admin según configuración.
- **Perfil**: logo, descripción, dirección/sector/horario, link externo, video, tags, campo SEO, con un checklist que recuerda al dueño qué le falta completar.
- **Galería**: hasta 10 fotos, con orden y foto principal.
- **"Actividad de hoy"**: oferta del día (con vencimiento configurable de 1 a 30 días), menú del día, avisos y promociones — cada uno un formulario simple e independiente.
- **Contenido según categoría**:
  - Gastronomía: carta (texto libre, link externo o PDF).
  - Alojamiento: precio desde, check-in/out, habitaciones y servicios (texto libre), políticas.
  - Museos: entradas (tarifas) y exposiciones (CRUD completo con fechas e imagen).
  - Cultura: agenda de eventos con CRUD completo, venta de entradas opcional vía Flow, y una vista "reel" pensada para grabar historias de redes sociales.
  - Tiendas/Artesanía: catálogo de productos simple (nombre, precio, descripción, imagen, orden).
- **Pindoor Recomienda**: el cliente solo puede ver si el admin escribió una recomendación sobre su negocio, no crearla.
- El cliente **no ve** ninguna métrica de visitas/engagement de su propia ficha — esa información solo existe en el panel de admin.

## Panel de gestión — Artista (`/artista/*`)

Perfil (disciplina, descripción, redes sociales), portafolio de imágenes, agenda de eventos propia (mismo formato que la de clientes), y gestión de banda (invitar/aceptar/quitar miembros por email).

## Panel de administración (`/admin/*`)

- Dashboard con totales de lugares/clientes, últimos registros, leads pendientes, conteo de shares y actividad reciente de clientes.
- Gestión de clientes: aprobación de negocios nuevos, historial de actividad por cliente, destacados de home, vincular un lugar existente a un usuario, activar/desactivar módulos por cliente.
- Dashboard de engagement de clientes: logins y acciones por día (últimos 30 días), y una segmentación automática (activo / tibio / inactivo / nunca conectó) — esto es solo para uso interno del admin.
- Gestión de usuarios, leads de contacto, artistas, categorías, blog, rutas, panoramas, importadores de eventos externos (Passline/Portaldisc), check-in de reservas, newsletter, distritos y experiencias.
