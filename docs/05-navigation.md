# 05 Navigation

- Status: Draft
- Last updated: 2026-03-28
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
- `Kunjungan`
- `Verifikasi Outlet`
- `Laporan`
- `Import Outlet`
- `Pengaturan`

## Supervisor Menu

- `Dashboard Cabang`
- `Aktivitas Saya`
- `Input Kunjungan`
- `Outlet`
- `Verifikasi Outlet`
- `Daftar Prospek`
- `Daftar NOO`
- `Laporan Cabang`

## Sales Menu

- `Dashboard Saya`
- `Input Kunjungan`
- `Riwayat Kunjungan`
- `Outlet Cabang`
- `Prospek Saya`

## SMD Menu

- `Dashboard Saya`
- `Input Kunjungan`
- `Riwayat Kunjungan`
- `Outlet Cabang`
- `Aktivitas Saya`

## Important Navigation Behavior

- `Input Kunjungan` opens directly to form for `sales` and `smd`.
- For `supervisor`, `Input Kunjungan` first asks for visit type: `sales` or `smd`.
- Non-admin users should never see cross-branch navigation targets.
- `Outlet` navigation is now available in the app shell as a dedicated master-data module.
