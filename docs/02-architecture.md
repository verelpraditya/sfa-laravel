# 02 Architecture

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Technical direction, constraints, and high-level architecture.

## Architecture Summary

The application should use a shared-hosting-friendly server-rendered architecture with lightweight frontend interactivity.

## Agreed Stack Direction

- Backend: `Laravel`
- Rendering: `Blade`
- Frontend interactivity: `Alpine.js`
- Styling: `Tailwind CSS`
- Charts: `Chart.js`
- Font: `Inter` from `fonts.bunny.net`
- Database: `MySQL` or `MariaDB`
- Auth starter: `Laravel Breeze (Blade)`

## Why This Stack

- Shared hosting usually works better with classic PHP apps.
- Server-rendered pages keep infrastructure simple.
- Alpine gives modern interactivity without SPA complexity.
- Tailwind speeds up consistent UI implementation.
- Chart.js covers dashboard visuals without heavy frontend frameworks.

## Rendering Model

- Main pages are server-rendered.
- Interactive widgets use Alpine and small AJAX endpoints.
- No full SPA shell.
- No persistent Node process needed in production.

## Frontend Interaction Pattern

- Blade for layouts, pages, and reusable components.
- Alpine for dropdowns, tabs, conditional sections, GPS triggers, image preview, and autocomplete behavior.
- Fetch JSON from lightweight Laravel endpoints for search and dashboard filters.

## Auth Foundation

- Breeze Blade is installed as the authentication base.
- Login uses `username` instead of `email`.
- Public registration routes are disabled.
- Session-based auth remains the primary web auth mechanism.

## Access Control Foundation

- A `role` middleware alias is registered in `bootstrap/app.php`.
- Role-specific workspace routes are available as groundwork for later module isolation.
- Branch scoping at query level is still the next implementation layer.

## Responsive Strategy

- Mobile-first for field forms.
- Desktop tables for reporting-heavy pages.
- Mobile cards for lists and activity history.
- Desktop sidebar plus mobile offcanvas navigation.

## Environment Constraint

The shell initially resolved `php` to `7.3.33`, but the project target remains PHP `8.2+`. Laravel 12 scaffold is already created using a PHP `8.2.30` binary. Future CLI usage should also point to PHP `8.2+`.

## Recommended Deployment Assumption

Prefer hosting that supports:

- PHP `8.2+`
- MySQL/MariaDB
- writable `storage/`
- symlink or equivalent for public file serving
- cron if scheduler is needed later

## Key Data Flow

1. User logs in.
2. App scopes access by role and branch.
3. User opens visit form.
4. Outlet is searched by AJAX autocomplete.
5. Existing outlet is selected or new outlet is created inline.
6. Visit data and evidence are submitted.
7. Supervisor verifies outlet and fills `official_kode` when needed.
8. Dashboards aggregate visits, outlet growth, and branch performance.

## Timezone Direction

- Branches store timezone using IANA values such as `Asia/Jakarta` and `Asia/Makassar`.
- Visit timestamps can be rendered according to branch timezone for branch-level operational views.
