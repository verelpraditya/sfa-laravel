# 05 Navigation

- Status: Draft
- Last updated: 2026-04-10
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

## Collapsible Sidebar Groups

The sidebar uses collapsible groups to organise menu items. Implementation uses Alpine.js `x-data` with the `x-collapse` plugin.

### Sidebar Structure

- **Dashboard** (ungrouped)
- **Master Data** group (admin_pusat only): Cabang, User
- **Outlet** group: Outlet, Verifikasi Outlet, Outlet Inactive, Prospek, Deteksi Duplikat
- **Kunjungan** group: History Kunjungan, Kunjungan Sales, Kunjungan SMD
- **Monitoring** group: Laporan
- **Profil** (ungrouped)

### Behavior

- Each group header toggles its child list open/closed.
- A chevron icon on the group header rotates to indicate open/closed state.
- Groups auto-open when a child item matches the current route (active state).
- The same collapsible group structure is used on both the desktop sidebar and the mobile offcanvas menu.
- Ungrouped items (Dashboard, Profil) render as standalone links outside any collapsible section.

## Important Navigation Behavior

- Root URL redirects directly to `Login`.
- `Kunjungan Sales` opens directly to sales visit flow for `sales` and `supervisor`.
- `Kunjungan SMD` opens directly to SMD visit flow for `smd` and `supervisor`.
- Non-admin users should never see cross-branch navigation targets.
- `Outlet` navigation is available in the app shell as a dedicated master-data module.
- `Laporan` is visible only to `admin_pusat` and `supervisor`.
- `Verifikasi Outlet` is a pending-only supervisor/admin workflow.
