# 11 Decisions Log

- Status: Active
- Last updated: 2026-03-28
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
- Sales can change outlet type from `prospek` to `noo`.
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
