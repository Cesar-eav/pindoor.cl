# Flujo: Activar (Reclamar) Perfil de Negocio

Permite que el dueño real de un lugar tomen control de un `PuntoInteres` "básico" (creado por el admin o importado, sin dueño real) y lo convierta en su perfil de negocio gestionable.

> No confundir con el flujo de **aprobación de negocios nuevos** (`estado_aprobacion`, `AdminController::aprobarCliente/rechazarCliente`, notificación `NegocioPendienteAprobacion`), que es para altas nuevas hechas por un cliente vía onboarding. Este documento cubre el reclamo de un punto **ya existente** sin dueño.

## Concepto: perfil "básico"

`PuntoInteres::esBasico()` devuelve `true` cuando el punto pertenece al usuario "sistema" (`users.es_sistema`, placeholder creado con el comando `CrearUsuarioSistema`). Mientras es básico:

- No puede tener módulos activos (`moduloActivo()` los bloquea).
- La ficha pública (`puntos/show.blade.php`) muestra el CTA **"¿Eres el propietario de este espacio? → Activa tu perfil gratis"**, que enlaza a `puntos.activar`.

## Modelo `ReclamoNegocio`

Tabla `reclamos_negocio` (migración `2026_08_25_120000_create_reclamos_negocio_table.php`):

```
id, punto_id (FK puntosinteres, cascadeOnDelete), name, email, whatsapp (nullable),
status (default 'pending'), activation_token (nullable, unique), token_expires_at (nullable),
timestamps
```

Estados: `pending` → `approved` → `completed`, o `rejected`.

- `vigente()`: `true` solo si `status === 'approved'` AND tiene `activation_token` AND `token_expires_at` en el futuro (48h desde la aprobación).
- `completar(User $user)`: vincula el punto (`user_id`, `es_cliente = true`, `modulos_habilitados` = default de la categoría) y marca el reclamo `completed`.

## Rutas

**Públicas — solicitud de reclamo:**

| Método | URL | Nombre | Controlador |
|---|---|---|---|
| GET | `/lugar/{slug}/activar` | `puntos.activar` | `PuntoInteresController@activar` |
| POST | `/lugar/{slug}/activar` | `puntos.activar.store` | `PuntoInteresController@activarStore` |

**Públicas — activación con token** (funcionan con o sin sesión iniciada):

| Método | URL | Nombre | Controlador |
|---|---|---|---|
| GET | `/activar-perfil/{token}` | `reclamo.activar` | `ReclamoActivacionController@activar` |
| POST | `/activar-perfil/{token}` | `reclamo.activar.store` | `ReclamoActivacionController@store` |

**Admin** (`middleware(['auth','verified','role:admin'])`, prefijo `/admin`):

| Método | URL | Nombre | Controlador |
|---|---|---|---|
| GET | `/admin/reclamos` | `admin.reclamos.index` | `Admin\ReclamoController@index` |
| PATCH | `/admin/reclamos/{reclamo}/aprobar` | `admin.reclamos.aprobar` | `Admin\ReclamoController@aprobar` |
| PATCH | `/admin/reclamos/{reclamo}/rechazar` | `admin.reclamos.rechazar` | `Admin\ReclamoController@rechazar` |

**Admin — vínculo manual sin token** (vincula un usuario `cliente` ya existente):

| Método | URL | Nombre | Controlador |
|---|---|---|---|
| GET | `/admin/puntos/{punto}/activar-cliente` | `admin.clientes.activar.form` | `AdminController@mostrarActivarCliente` |
| POST | `/admin/puntos/{punto}/activar-cliente` | `admin.clientes.activar` | `AdminController@activarCliente` |

## Diagrama del flujo

```
Visitante                    Admin                        Sistema
    │
    ├─ GET/POST /lugar/{slug}/activar
    │  (name, email, whatsapp)
    │                                                     ReclamoNegocio (pending)
    │                                                     ↓
    │                                        NuevoReclamoNotification (mail+Telegram)
    │                                        → soporte@pindoor.cl, cesar.eav@gmail.com
    │
    │                             GET /admin/reclamos
    │                             ├─ Aprobar ──────────→  status=approved
    │                             │                       activation_token = random(40)
    │                             │                       token_expires_at = +48h
    │                             │                       ReclamoAprobadoNotification (mail)
    │                             │                       → email del solicitante
    │                             └─ Rechazar ──────────→ status=rejected (sin notificación)
    │
    ├─ Abre link del correo:
    │  GET /activar-perfil/{token}
    │
    ├── ¿token vigente? ── no ──→ vista activacion-invalida
    │
    ├── ¿sesión iniciada?
    │     sí, email coincide ──→ completar($user) ──→ redirect cliente.perfil.editar
    │     sí, email distinto ──→ activacion-invalida (emailIncorrecto)
    │     no, email ya tiene cuenta ──→ vista pide iniciar sesión con ese correo
    │     no, sin cuenta ──→ vista activacion-aceptar:
    │           ├─ Google OAuth (con reclamo_token en sesión)
    │           └─ Formulario (name, password) → POST reclamo.activar.store
    │                 → crea User (email fijo, no editable, email_verified_at=now())
    │                 → Auth::login
    │                 → completar($user) ──→ redirect directo a cliente.perfil.editar
    │                    (sin pantalla de "verifica tu email"; el punto ya trae
    │                     título/descripción/ubicación, el mensaje invita a
    │                     actualizarlos cuando el cliente quiera)
```

## Paso a paso

### A. Solicitud pública

1. El visitante llena el formulario (`puntos/activar.blade.php`): `name` (requerido), `email` (requerido), `whatsapp` (opcional).
2. `activar` (GET) valida que el punto sea `publico()` y `esBasico()` (`abort_unless`, 404 en caso contrario) y muestra el formulario.
3. `activarStore` (POST) re-valida lo mismo, crea el `ReclamoNegocio` en `pending`, y dispara `NuevoReclamoNotification` (mail + Telegram) a `soporte@pindoor.cl` y `cesar.eav@gmail.com`. Redirige con mensaje de éxito.

### B. Revisión admin (`/admin/reclamos`)

4. `index`: listado paginado (15/página) de `ReclamoNegocio::with('punto')->latest()` con badges de estado.
5. `aprobar`: exige `status === 'pending'` (409 si no); genera `activation_token` y `token_expires_at` (+48h); envía `ReclamoAprobadoNotification` al email del solicitante con el link de activación.
6. `rechazar`: exige `status === 'pending'`; solo cambia a `rejected`, sin notificar.

### C. Activación con token

7. `ReclamoActivacionController@activar`:
   - Token inexistente o no `vigente()` (rechazado, completado o vencido) → `reclamo.activacion-invalida`.
   - Con sesión activa y email coincide → `completar($user)` directo → redirect `cliente.perfil.editar`.
   - Con sesión activa y email distinto → `activacion-invalida` (pide cerrar sesión).
   - Sin sesión → `reclamo.activacion-aceptar`, distinguiendo si ya existe un `User` con ese email.
8. Vista `activacion-aceptar`: si ya existe cuenta, solo pide iniciar sesión y reabrir el link. Si no existe, ofrece Google OAuth o formulario propio (email prellenado y no editable, `name` + `password`/`password_confirmation`).
9. `ReclamoActivacionController@store`: valida reclamo vigente (404 si no), bloquea si ya existe un `User` con ese email (409), crea el `User` (`type = cliente`, email del reclamo, `email_verified_at = now()`), dispara `Registered`, hace login, llama `completar($user)` y redirige directo a `cliente.perfil.editar`. El email se marca verificado en la creación (mismo patrón que `SocialiteController`) porque ya se probó real al enviarle el link de activación y abrirlo — así se evita el paso extra de "verifica tu email" que exige el middleware `verified` de las rutas `cliente.*`. El mensaje de éxito deja claro que el perfil ya tiene la ficha básica (título, descripción, ubicación) y que puede actualizarla cuando quiera, ya que el `PuntoInteres` reclamado la trae desde que era un punto básico.
10. Camino Google (`Auth\SocialiteController`): `redirect` guarda `reclamo_token` en sesión antes de ir a Google; `callback` recupera el token, valida que el email de Google coincida con el del reclamo, y si coincide llama `completar($user)`; si no, vuelve a `reclamo.activar` con error.

### D. Alternativa manual del admin (sin token)

11. `AdminController@mostrarActivarCliente` / `activarCliente`: vincula directamente un `User` tipo `cliente` ya existente a un punto básico, sin token ni email. Si no hay usuarios disponibles, la vista sugiere reclamar desde la ficha pública.

## Vistas

| Vista | Layout | Propósito |
|---|---|---|
| `puntos/activar.blade.php` | `layouts.pindoor` | Formulario público inicial de solicitud |
| `reclamo/activacion-aceptar.blade.php` | `x-guest-layout` | Crear cuenta (form o Google) o pedir login |
| `reclamo/activacion-invalida.blade.php` | `x-guest-layout` | Token vencido/inexistente/usado, o email incorrecto |
| `admin/reclamos.blade.php` | `x-admin-layout` | Listado admin con acciones Aprobar/Rechazar |
| `admin/clientes-activar.blade.php` | `x-admin-layout` | Vinculación manual admin → usuario existente |
| `puntos/show.blade.php` (~L1450-1460) | — | CTA "¿Eres el propietario de este espacio?" (solo si `esBasico()`) |

## Notificaciones

- `NuevoReclamoNotification` (`ShouldQueue`, canales `mail` + `TelegramChannel`): al crear el reclamo, a `soporte@pindoor.cl` y `cesar.eav@gmail.com`.
- `ReclamoAprobadoNotification` (`ShouldQueue`, canal `mail`): al aprobar, al email del solicitante, con link de activación y aviso de vencimiento en 48h.
- Rechazar no notifica al solicitante.
- Queue en modo `sync` → se procesan inmediatamente.

## Validaciones y seguridad

- El punto debe ser `publico()` y `esBasico()` para solicitar o mostrar el CTA de reclamo.
- `aprobar`/`rechazar` exigen `status === 'pending'` (409 si no) — evita doble resolución.
- `vigente()` centraliza validez del token: aprobado + token presente + no vencido.
- El email nunca es editable en el formulario de creación de cuenta — siempre es el del `ReclamoNegocio`, para que nadie use un token ajeno con otro correo.
- Si ya existe un `User` con ese email, se bloquea la creación (409) y se dirige a login.
- La coincidencia de email es obligatoria tanto en sesión activa como en el callback de Google antes de `completar()`.

## Migraciones

- `2026_08_25_120000_create_reclamos_negocio_table.php` — crea `reclamos_negocio`.
- `2026_07_30_143801_add_es_sistema_to_users_table.php` (contexto previo) — agrega `es_sistema` a `users`, base de `PuntoInteres::esBasico()`.
