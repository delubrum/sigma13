# SIGMA PROJECT ARCHITECTURE RULES
- SERVER: FrankenPHP (Worker Mode / Octane).
- DB: PostgreSQL 18.
- STACK: HALT (HTMX, Alpine.js, Laravel 13, Tailwind).
- CACHE: Valkey (Redis compatible).
- NO ROADRUNNER: Está prohibido instalar 'spiral/roadrunner' o dependencias de 'ext-sockets'.
- PHILOSOPHY: KISS (Keep It Simple, Stupid). No añadas librerías sin preguntar.
- DOCKER: El Dockerfile es inamovible. No intentes corregir errores de PHP instalando extensiones de red adicionales.


# SIGMA: Architecture & Custom Rules

**Core Philosophy: KISS (Keep It Simple, Stupid).** We prioritize low technical complexity and high operational efficiency. Full compliance with `AGENTS.md` and `Laravel Boost` is mandatory.

---

## 1. Core Philosophy: KISS & Sobriety
* **Principle:** Keep It Simple, Stupid (Low-complexity architecture). If a solution is over-engineered, it is not SIGMA.
* **UI/UX:** Industrial Aesthetic (Grayscale, high data density, sober design).
* **Language:** Technical documentation and code (variables, methods, comments) in **English**. Final User Interface and system messages in **Spanish**.

---

## 2. Tech Stack (HALT + Octane)
* **Runtime:** Laravel 13 + **Octane (FrankenPHP)**.
* **Frontend:** **HALT Stack** (HTMX, Alpine.js, Tailwind 4, Blade).
* **Database:** PostgreSQL 18 (Native JSONB operations).
* **Search:** **Laravel Scout + Meilisearch** (Global search and remote Tabulator filtering).

---

## 3. Logic Modeling (Hybrid MVC/ADR)
We use **Spatie Data** as the single source of truth for universal typing.

| Complexity | Pattern | Components                                      |
| :--------- | :------ | :---------------------------------------------- |
| **Low**    | **MVC** | `Resource Controller` + `Spatie Query Builder`. |
| **High**   | **ADR** | `Lorisleiva\Action` + `ADR Handler`.            |

* **Rule:** Only use ADR for side effects, AI integration, or complex response flows. **Do not use Actions in MVC.**

---

## 4. UI Components & Interaction (JS Tools)
For building forms and dashboards, use exclusively:
* **Grids:** **Tabulator** (Remote data loading).
* **Inputs:** **FilePond** (Uploads), **Flatpickr** (Dates), **Slim Select** (Selects).
* **Charts:** **Chart.js**.
* **Sync:** Mandatory use of `HX-Trigger` header for server-side UI orchestration.

### Arquitectura de Comunicación (SIGMA Orchestration)

**Frontend:** Orquestador global basado en eventos HTMX.

**Backend:** Trait `HtmxOrchestrator` para emisión fluida de triggers.

**Regla de Oro:** IDs de elementos (KISS) y asincronía total (Queues) para servicios externos.

#### Orquestador JS (sigma-orchestrator.js)
El orquestador escucha el header `Hx-Trigger` y ejecuta acciones:
```javascript
// notify: {type: 'success'|'error', message: '...'} -> Notyf
// close-modals: {ids: ['#modal-id']} -> Cierra elementos
// refresh-divs: {ids: ['#counter-id']} -> htmx.trigger(el, 'refresh')
// refresh-tables: {ids: ['#grid-id']} -> el.tabulator.setData()
```

#### Backend Trait (HtmxOrchestrator)
```php
use App\Support\HtmxOrchestrator;

// Métodos: hxNotify(), hxCloseModals(), hxRefresh(), hxRefreshTables()
// hxResponse($data, $status) -> consolida el header HX-Trigger JSON
```

#### Notificaciones (Infraestructura)
* **Email (Resend):** Mailables de Laravel (Blade/Markdown).
* **Telegram:** Alertas de sistema.
* **ENVÍO:** Siempre mediante Jobs/Queues.

#### Ejemplo: Aprobación de Presupuesto
1. Enviar email de aprobación (Resend - Queue)
2. Notificar Telegram (Queue)
3. Return JSON con triggers:
   - `notify`: 'Presupuesto #123 aprobado'
   - `close-modals`: {ids: ['#approve-form']}
   - `refresh-divs`: {ids: ['#pending-counter', '#total-balance']}
   - `refresh-tables`: {ids: ['#budgets-grid']}

### HTMX Best Practices
* Use `hx-post`/`hx-get` for form submissions and data loading.
* Always use `hx-swap="none"` for redirects or full page navigation.
* Use `hx-indicator` to show loading states on buttons/forms.
* Use `hx-on::after-request` to handle response and trigger HX-Events.
* Include `@csrf` directive on all POST forms.
* Include `@honeypot` directive on all forms for spam protection.

### Alpine.js Best Practices
* Use `x-data` for component state management.
* Use `x-show`/`x-if` for conditional rendering.
* Use `x-model` for two-way data binding.
* Use `x-init` for initialization logic.
* Use `x-transition` for smooth transitions.
* Keep Alpine logic minimal; prefer server-side rendering with HTMX.

---

## 5. Validation, Quality & Security (Implementation Info)
Pre-configured system; agents must respect integrity and run the following tools:
* **Quality & Statics:** Code must pass **Larastan 9** (Max Level), **Pest Architecture**, **Rector** (PHP upgrades/standardization), and **Pint** (Styling).
* **Auth:** **Laravel Fortify** (Headless).
* **Security:** **Spatie CSP** + **Spatie Honeypot** for form protection.
* **Flat RBAC:** Permissions reside in the `users.permissions` JSONB column.
* **Permission Usage:** Validation is consumed via `@can(PERMISSION_ID)` for single checks, or `@canany([ID1, ID2])` for multiple. No relational tables or Policies.

### CSP Configuration
* CSP is configured in `config/csp.php`.
* Use `@cspNonce` directive in blade templates for inline scripts/styles.
* For HTMX/Alpine inline scripts, use `Nonce` attribute: `<script nonce="{{ csp_nonce() }}">`.

### Honeypot Configuration
* Honeypot is configured in `config/honeypot.php`.
* Include `@honeypot` directive in all forms.
* Configured with 1-second timestamp validation.

### Debug Tools
* **Telescope:** Application debugging at `/telescope` (only `admin@sigma.com`).
* **Debugbar:** Integrated in development mode (bottom bar).
* **Pail:** Real-time log viewer via `laravel/pail`.

---

## 6. AI & Worker Safety (Octane)
* **AI:** Integrated via `laravel/ai` Facade inside an **Action**. 
    * **Role Context:** "Senior Industrial Maintenance & RCFA Expert".
* **Stateless:** No state storage in static properties (Worker Mode).
* **DI:** Inject `Request` into methods, never into Singleton constructors.
