# 00 Getting Started

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Main entry point for humans and AI agents working on this project.

## Project Summary

This project is a web-based SFA application for a distributor with multiple branches in different cities. The app focuses on field visits, outlet master growth during visits, supervisor verification, and branch performance monitoring.

## Recommended Reading Order

1. `README.md`
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

## Glossary

- `SFA`: Sales Force Automation.
- `Outlet`: Store/customer/shop visited in the field.
- `Official kode`: Official store code sourced from Accurate or company master data.
- `Prospek`: Prospect outlet, not yet official.
- `NOO`: New outlet opening/new outlet status before official code is assigned.
- `Pelanggan lama`: Existing official customer/outlet with official code.
- `SMD`: Field role with operational activities like taking PO, tidying display, collecting payments.

## Working Principles

- Keep the system shared-hosting friendly.
- Prefer server-rendered pages with lightweight interactivity.
- Prioritize mobile-first UX for field users.
- Treat documentation as a living source of truth.
- Preserve audit trail; submitted visits are not editable by supervisors.

## Environment Notes

- Laravel 12 scaffold is now present in the repository.
- The shell initially resolved `php` to version `7.3.33`.
- Laravel setup was completed by explicitly using a PHP `8.2.30` binary and a newer Composer runtime.
- Going forward, use Herd or another PHP `8.2+` CLI when running Artisan and Composer commands.
