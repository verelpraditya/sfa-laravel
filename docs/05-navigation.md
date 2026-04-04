# 05 Navigation

- Status: Draft
- Last updated: 2026-03-31
- Purpose: Menu structure and navigation rules per role.

## Navigation Pattern

- Desktop: left sidebar.
- Mobile: offcanvas hamburger menu.
- Reporting pages: table-first on desktop, card-first on mobile.

## Admin Pusat Menu

- `Dashboard`
- `Cabang`
- `User`
- `Outlet`
- `History Kunjungan`
- `Verifikasi Outlet`
- `Laporan`

## Supervisor Menu

- `Dashboard`
- `History Kunjungan`
- `Outlet`
- `Verifikasi Outlet`
- `Prospek`
- `Outlet Inactive`
- `Kunjungan Sales`
- `Kunjungan SMD`
- `Laporan`

## Sales Menu

- `Dashboard`
- `History Kunjungan`
- `Outlet`
- `Prospek`
- `Kunjungan Sales`

## SMD Menu

- `Dashboard`
- `History Kunjungan`
- `Outlet`
- `Kunjungan SMD`

## Important Navigation Behavior

- Root URL redirects directly to `Login`.
- `Kunjungan Sales` opens directly to sales visit flow for `sales` and `supervisor`.
- `Kunjungan SMD` opens directly to SMD visit flow for `smd` and `supervisor`.
- Non-admin users should never see cross-branch navigation targets.
- `Outlet` navigation is available in the app shell as a dedicated master-data module.
- `Laporan` is visible only to `admin_pusat` and `supervisor`.
- `Verifikasi Outlet` is a pending-only supervisor/admin workflow.
