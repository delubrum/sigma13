# SIGMA ARCHITECTURE RULES

Innegotiable rules for SIGMA project.

## 1. TECH STACK
- Server: FrankenPHP (Worker + Octane)
- DB: PostgreSQL 18 (JSONB for config, RBAC, metadata)
- Backend: Laravel 13 + Lorisleiva Actions + Spatie Laravel Data
- Frontend: HTMX 2 + Alpine.js + TailwindCSS 4

## 2. CORE PHILOSOPHY
- KISS & Sobriety: Minimal complexity, maximum efficiency. No new libraries without approval.
- UI/UX: Industrial style (B/W/Grey, high data density, native Tailwind Dark Mode).
- Reusability: Always check `App\Data\Shared`, `App\Actions\Shared` and `components` first. Extend, never duplicate.
- Language: Code + technical docs → English. UI + user messages → Spanish.

## 3. FRONTEND RULES
- Alpine.js: Only ephemeral UI state (toggles, modals, dropdowns). Never persistent data.
- HTMX: Always use HTMX Orchestrator for responses, modals, notifications and DOM events.

## 4. BACKEND RULES
- PHPStan: Strict max level. No `mixed`. Full strict typing in Spatie Data DTOs.
- Actions: Every endpoint/business logic is one Action. Module actions centralize persistence + HTMX response.
- Spatie Data: 
  - Tabulator tables → `Table` DTO per row.
  - Forms → Always validate with Spatie Data DTO. Never use `$request->all()` directly.

## 5. UI COMPONENTS (Data-Driven)
- Modals: Always use `components.new-modal` + `Config` + `Field` DTOs. Prefer DTO config over custom Blade.
- Detail Layout (HasDetail):
  - 0 Tabs: Simple modal.
  - 1 Tab: Modal + sidebar (no top tabbar). Route/name: `detail`.
  - Multi-Tabs: Modal + sidebar + tabbar (like Assets module).
- Nested modals: Use `#modal-body-2` + `open-modal-2` event.

## 6. CODE QUALITY
- Aggressive renaming for better semantics.
- Standard for single-tab detail: `App\Actions\Modulo\Tabs\Detail.php`.

## 7. AI & OPTIMIZATION
- LLMs: Always use `Prism` facade.
- Search: Use PostgreSQL `pg_trgm` indexes for fuzzy search.

---

**Core Rule:** DRY + Config-Driven. Reuse first, create only when necessary.