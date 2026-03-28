# SFA Distributor Web App

Lightweight web-based SFA for multi-branch distributors, focused on outlet visit reporting, outlet master growth from field activity, and branch performance monitoring.

## Documentation Structure Overview

### Tier 1: Getting Started

| File | Purpose |
|---|---|
| `docs/00-getting-started.md` | Main entry point, reading order, glossary |
| `README.md` | Quick index of all documents |

### Tier 2: Core Documentation

| File | Purpose | Status |
|---|---|---|
| `docs/01-requirements.md` | Business requirements and acceptance criteria | Draft |
| `docs/02-architecture.md` | System architecture and stack decisions | Draft |
| `docs/03-database-schema.md` | ERD, tables, relationships, enums | Draft |
| `docs/04-rbac-permissions.md` | Role and access matrix | Draft |
| `docs/05-navigation.md` | Menu structure and route direction | Draft |
| `docs/06-pages-and-forms.md` | Page specs and form validation rules | Draft |
| `docs/07-dashboard-and-reports.md` | Dashboard content and reporting rules | Draft |
| `docs/08-api-and-ajax.md` | AJAX endpoints and response contracts | Draft |
| `docs/09-ui-system.md` | Visual system and responsive UI rules | Draft |
| `docs/10-implementation-roadmap.md` | Phased implementation roadmap | Draft |
| `docs/11-decisions-log.md` | Major product and technical decisions | Active |
| `docs/12-open-questions.md` | Pending questions, risks, follow-ups | Active |

## Current Foundation Status

- Business rules are documented.
- Shared-hosting-friendly architecture is documented.
- Database schema draft is documented.
- RBAC, navigation, forms, dashboards, and UI system are documented.
- Laravel 12 scaffold has been created successfully.
- Frontend foundation now includes `Alpine.js`, `Chart.js`, Tailwind v4, and Bunny `Inter` font setup.
- A branded landing page has replaced the default Laravel welcome screen.
- Breeze Blade auth is installed with username-based login and public registration disabled.
- Branch groundwork, role fields, active-user login guard, and seeded demo users are now available.
- Core SFA schema for `outlets`, `visits`, sales details, SMD details, and audit logs is now in place.
- Role middleware alias `role` is registered for route protection groundwork.
- Outlet module foundation is now available with branch-scoped listing, create/edit flow, and AJAX autocomplete search.
- Frontend assets build successfully and the default test suite passes.
- Current shell still defaults to PHP `7.3.33`, so project commands should use PHP `8.2+` from Herd or an explicit PHP 8.2 binary.

## Reading Order

1. `docs/00-getting-started.md`
2. `docs/01-requirements.md`
3. `docs/02-architecture.md`
4. `docs/03-database-schema.md`
5. `docs/04-rbac-permissions.md`
6. `docs/05-navigation.md`
7. `docs/06-pages-and-forms.md`
8. `docs/07-dashboard-and-reports.md`
9. `docs/08-api-and-ajax.md`
10. `docs/09-ui-system.md`
11. `docs/10-implementation-roadmap.md`
12. `docs/11-decisions-log.md`
13. `docs/12-open-questions.md`

## Maintenance Rule

These docs are the living source of truth. Any major change to requirements, architecture, data model, UI, or workflow should update the relevant document and `docs/11-decisions-log.md`.
