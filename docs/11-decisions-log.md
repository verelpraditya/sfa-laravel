# 11 Decisions Log

- Status: Active
- Last updated: 2026-04-11
- Purpose: Record major product and technical decisions.

## 2026-03-28

- Chosen architecture direction: `Laravel + Blade + Alpine.js + Tailwind CSS + Chart.js + MySQL/MariaDB`.
- Target deployment: shared hosting friendly setup.
- Frontend is not a full SPA.
- UI is mobile-first, with desktop tables and mobile cards.
- Navigation pattern is desktop sidebar plus mobile offcanvas.
- Font choice is `Inter` from Bunny Fonts.
- Visual direction is light, clean, and field-friendly instead of dark theme.
- Sales can access only sales visit form.
- SMD can access only SMD visit form.
- Supervisor can create either sales-type or SMD-type visits, only for self.
- Supervisor can verify outlets and fill `official_kode`.
- Supervisor cannot edit or delete submitted visits.
- Outlet search should be interactive without page reload.
- `official_kode` is the official replacement term for Accurate code.
- Sales can view all outlets in their own branch.
- Laravel 12 scaffold was created successfully using PHP `8.2.30` and Composer `2.9.5`.
- Current shell still defaults to PHP `7.3.33`, so future CLI work should use Herd or another explicit PHP `8.2+` executable.
- Frontend packages `alpinejs` and `chart.js` were installed into the Laravel scaffold.
- The default Laravel landing page was replaced with a branded project home screen that matches the agreed UI direction.
- Asset build now passes and the default PHPUnit suite passes under PHP `8.2.30`.
- Breeze Blade was selected and implemented for auth.
- Login now uses `username` instead of `email`.
- Public registration was disabled.
- User schema now includes `username`, `role`, `branch_id`, and `is_active` groundwork.
- Seed data now includes one branch and demo accounts for `admin_pusat`, `supervisor`, `sales`, and `smd`.
- Core SFA schema was implemented with tables for outlets, visits, sales/SMD details, and audit logs.
- A reusable `role` middleware alias was registered to support route-level access control.
- Outlet module was implemented as a dedicated master-data feature, not only as inline visit data.
- Outlet autocomplete currently uses a branch-scoped JSON endpoint and is already wired into the outlet module UI preview.
- Sales visit implementation reuses the same outlet autocomplete endpoint and supports inline new-outlet creation when no outlet is found.
- Sales visit form currently stores proof photo on the `public` disk and timestamps visits at submit time.
- SMD visit form reuses the same outlet selection pattern but supports multiple activities and conditional file/nominal requirements.
- Dashboard no longer uses placeholder numbers; it now reads real aggregates from the database by role scope.
- Outlet verification is implemented as a dedicated supervisor/admin module instead of overloading the standard outlet edit flow.
- Branch master now stores IANA timezone values so branch views can render visit times according to local branch time.
- User master is implemented as an admin-only module for creating and updating internal accounts, roles, branches, and activation state.
- Operational lists are implemented as dedicated filtered outlet views rather than asking users to rely on master outlet filters every time.
- Reporting is implemented as a shared module with role-aware datasets and simple CSV export instead of introducing a heavy export package.

## 2026-03-31

- Public root path now redirects directly to the login page.
- Login page was redesigned for a production-ready mobile-first experience.
- Report menu and report routes are restricted to `admin_pusat` and `supervisor`; `sales` and `smd` no longer have report access.
- Visit forms now prefer camera capture on mobile browsers, compress photos client-side, show image previews, and preserve non-file input state after validation errors.
- Uploaded visit images now use readable filenames in the format `username_outlet_YYYYMMDD_HHMMSS`.
- Outlet lifecycle was simplified into one status model: `prospek`, `pending`, `active`, `inactive`.
- Legacy outlet flow fields `outlet_type` and `verification_status` were removed from the active business flow.
- User-facing inline outlet choices in sales/SMD forms remain `Prospek`, `NOO`, and `Pelanggan Lama`, but they now map internally to the unified outlet statuses.
- Supervisor outlet verification was narrowed to a pending-only workflow where the supervisor fills `official_kode` and activates the outlet.
- The separate `Outlet Pending` menu was removed in favor of a single focused `Verifikasi Outlet` workflow.
- Dashboard KPI `Sales Amount Hari Ini` combines sales order and SMD PO, while `Collection Hari Ini` combines sales receivable input and SMD payment collection input.

## 2026-04-01

- Sales and SMD visit submissions now use a dedicated submission token so repeated taps on slow networks do not create duplicate visits.
- Submit buttons on visit forms are locked after the first submit and switch to a `Menyimpan...` state.
- SMD display evidence was expanded from a single image to a `1-10` photo workflow while keeping legacy single-photo data readable.
- SMD display photo UX was changed to a camera-first, one-photo-at-a-time flow with compact status rows and `+ Tambah Foto`.
- Visit detail page now shows an embedded OpenStreetMap view from saved coordinates and provides a Google Maps shortcut.
- Prospect operational list now shows last visit time and last visiting user as lightweight follow-up context.

## 2026-04-02

- Mobile UI was polished again with tighter typography hierarchy, stronger contrast, clearer CTA styles, and more consistent reusable components.
- Login page was simplified and the password reveal action was reduced to a clean eye icon.
- Sales visit form now supports the compatibility condition `order_by_wa`, which currently behaves like `buka` for nominal input purposes.
- `official_kode` input now auto-removes spaces and converts to uppercase in sales, SMD, outlet master, and supervisor verification flows.

## 2026-04-10

- UI/UX was migrated from glassmorphism to a clean-colorful design system. All major pages now use gradient hero headers (`app-hero-gradient`), colored KPI cards with left accent bars, icon-based info rows for detail pages, and dark pill toast notifications. Sidebar remains dark theme.
- Sales visit form and SMD visit form were intentionally left unchanged — user is satisfied with their current design.
- Visit edit and delete was added for `admin_pusat` and `supervisor`. Supervisor is scoped to own branch. Editable fields include outlet, date, condition, amounts, activities, and notes. Photos and GPS remain immutable (field evidence integrity). Delete performs hard delete with cascade (details, activities, display photos) and removes physical files from storage.
- Outlet detail page (show) was created with 4 sections: outlet snapshot with info rows, KPI stats (total visits, sales, collection, last visit), OpenStreetMap embed from last visit coordinates, and paginated visit timeline.
- Outlet index and operational list pages were redesigned with gradient hero headers and added "Detail" buttons linking to the new show page.
- Outlet delete was restricted to `admin_pusat` only and is blocked when the outlet has any linked visits. This prevents accidental loss of visit business records.
- Duplicate outlet detection and merge tool was implemented for `admin_pusat` and `supervisor`. Detection uses case-insensitive name matching and official_kode matching within the same branch. Merge transfers all visits and audit logs from duplicates to a selected master outlet, then deletes the empty duplicates.
- Sidebar navigation was restructured with collapsible menu groups using Alpine.js. Groups: Master Data (admin only), Outlet (outlet-related pages), Kunjungan (visit-related pages), Monitoring (reports). Groups auto-expand when a child item is active.

## 2026-04-11

- Visit history KPI metrics were refactored to use separate DB aggregate queries instead of computing from the paginator collection. KPI now reflects the full filtered dataset, not just the 15 items on the current page.
- KPI metrics are now role-appropriate and non-redundant: sales sees Total Visit, Sales Amount, Collection; SMD sees Total Visit, PO Amount, Collection; supervisor/admin sees Total Visit, Sales Visit, SMD Visit, Sales Amount, Collection.
- Visit history mobile view was refactored from server-rendered Blade `@forelse` to Alpine.js infinite scroll. Page 1 data is embedded server-side via `@js()` for instant render (no initial fetch). Subsequent pages are loaded on demand using IntersectionObserver with a 200px root margin, fetching JSON from the same route via `Accept: application/json`.
- Desktop visit history table and pagination remain unchanged — server-rendered with Laravel paginator links.
- The `VisitHistoryController@index` method now serves dual responses: HTML for full page loads and JSON for mobile infinite scroll requests. No new routes were added.
