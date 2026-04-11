# 06 Pages and Forms

- Status: Draft
- Last updated: 2026-04-11
- Purpose: Page list, key form fields, and validation behavior.

## Core Pages

- Login
- Dashboard per role
- Outlet list
- Outlet verification list
- Sales visit form
- SMD visit form
- Visit history
- Visit detail
- Prospect list
- Reports

## Login Page

- Uses `username` and `password`
- Has remember-me option
- Supports show/hide password interaction
- Styled for mobile-first field usage
- Public root path redirects directly to login

## Shared Outlet Search Component

- Search by outlet name or `official_kode`
- AJAX autocomplete without page reload
- Show limited result list
- Allow inline new outlet form when not found

## Outlet Module Pages

- `GET /outlets`
  - desktop table view
  - mobile card view
  - filter by search and outlet status
  - gradient hero header
  - "Detail" button links to outlet show page
  - "Edit" button for authorized users
- `GET /outlets/{outlet}`
  - outlet detail/show page with 4 sections:
    1. Snapshot: name, branch, address, district, city, status badge, category badge, official kode, created by, verified by
    2. KPI stats: total visits, total sales, total collection, last visit date — always outlet-wide (all users), computed via SQL aggregates
    3. Location: embedded OpenStreetMap from last visit coordinates, Google Maps shortcut
    4. Timeline: visit history to this outlet, scoped per user for sales/SMD (sales only sees own visits, supervisor/admin sees all)
  - Desktop: paginated table (10 per page) with Laravel pagination links
  - Mobile: Alpine.js infinite scroll with embedded page 1 data and on-demand JSON fetch (same route, `Accept: application/json`)
  - "Edit" and "Hapus" buttons for authorized users
- `GET /outlets/create`
  - create master outlet form
- `GET /outlets/{outlet}/edit`
  - edit master outlet form
- `DELETE /outlets/{outlet}`
  - hard delete outlet, admin_pusat only
  - blocked if outlet has any linked visits
  - cascade deletes audit logs (outlet_status_histories, outlet_verification_logs)

## Outlet Verification Pages

- `GET /outlet-verifications`
  - pending outlet review list only
- `GET /outlet-verifications/{outlet}/edit`
  - review form with read-only outlet info and editable `official_kode` only

## Sales Visit Form

### Fields

- `outlet_id` or inline new outlet fields
- `name` when new
- `address` when new
- `district` when new
- `city` when new
- `category` when new
- `new_outlet_type` as user-facing inline choice:
  - `prospek`
  - `noo`
  - `pelanggan_lama`
- `official_kode` only when `pelanggan_lama`
- `outlet_condition`
- `order_amount`
- `receivable_amount`
- `latitude`
- `longitude`
- `visit_photo`
- `notes`

### Rules

- GPS required
- timestamp captured automatically
- visit proof photo required
- `official_kode` required when `pelanggan_lama`
- `official_kode` input auto-removes spaces and converts value to uppercase
- financial inputs only when `outlet_condition = buka` or `order_by_wa`
- new inline outlets are mapped to outlet status:
  - `prospek` -> `prospek`
  - `noo` -> `pending`
  - `pelanggan_lama` -> `active`
- mobile photo flow prefers camera capture via browser hint, shows preview, and compresses image client-side before upload
- uploaded photo filename is normalized to `username_outlet_YYYYMMDD_HHMMSS`
- validation messages are customized in Indonesian and old input is preserved after submit failure
- reusable mobile UI system now uses tighter typography, stronger contrast, and clearer CTA hierarchy across major pages

### Implemented pages

- `GET /sales-visits`
- `GET /sales-visits/create`
- `POST /sales-visits`

## SMD Visit Form

### Fields

- `outlet_id` or inline new outlet fields
- `activities[]`
- `po_amount`
- `payment_amount`
- `display_photos[]`
- `latitude`
- `longitude`
- `visit_photo`
- `notes`

### Rules

- at least one activity required
- GPS required
- timestamp captured automatically
- visit proof photo required
- `po_amount` required if `ambil_po`
- `payment_amount` required if `ambil_tagihan`
- minimum 1 display photo required if `merapikan_display`
- maximum 10 display photos per visit
- new inline outlets use the same outlet status mapping as sales visit form
- visit proof photo matches sales visit flow: camera hint, preview, and client-side compression
- display photo flow is optimized for field use:
  - camera-first, one photo at a time
  - `+ Tambah Foto` until maximum 10 photos
  - compact per-photo status such as `Foto 1 siap`
  - per-photo replace and remove actions
  - no large preview block

### Submission safety

- sales and SMD visit forms now include a submission token to prevent duplicate visit creation when the user taps submit multiple times.
- submit button is locked after first submit and changes to `Menyimpan...`.
- duplicate submission is redirected as a normal success response if the first request already created the visit.

### Implemented pages

- `GET /smd-visits`
- `GET /smd-visits/create`
- `POST /smd-visits`

## Supervisor Visit Entry

- No custom supervisor-only form.
- Supervisor selects visit type first.
- Selected type loads the same validation rules as the target form.

## Visit History

- `GET /visit-history` — paginated visit list with filters
- Desktop: server-rendered table with Laravel pagination (15 per page)
- Mobile: Alpine.js infinite scroll — page 1 data is embedded via `@js()` (no initial fetch delay), subsequent pages loaded via IntersectionObserver + JSON fetch
- Same route handles both HTML and JSON responses — JSON triggered by `Accept: application/json` header
- Filters (date range, type, condition, search) apply to both desktop and mobile via form submit (page reload)

### KPI metrics

- Computed from separate DB aggregate queries (not from paginator collection)
- Role-appropriate — no redundant data:
  - `sales`: Total Visit, Sales Amount, Collection
  - `smd`: Total Visit, PO Amount, Collection
  - `supervisor` / `admin_pusat`: Total Visit, Sales Visit, SMD Visit, Sales Amount, Collection

### Mobile infinite scroll behavior

- Page 1 data embedded server-side for instant render (no flicker)
- Sentinel element observed 200px before entering viewport
- Fetches next page via JSON endpoint with current filter params
- Shows loading spinner during fetch
- Displays "Semua data telah dimuat" when all pages loaded
- Observer disconnects after last page to avoid unnecessary checks
- Delete actions use inline forms with CSRF (same as desktop)

## Visit Detail

- Visit detail page shows:
  - visit summary
  - latitude and longitude
  - visit proof photo
  - SMD display photo gallery when available
  - embedded OpenStreetMap based on saved coordinates
  - shortcut link to Google Maps
  - Edit and Delete buttons for admin_pusat and supervisor (own branch)
  - delete confirmation modal with warning about permanent data loss

## Visit Edit

- `GET /visit-history/{visit}/edit` — edit form (admin_pusat and supervisor only)
- `PUT /visit-history/{visit}` — update visit
- `DELETE /visit-history/{visit}` — delete visit with cascade

### Editable fields

- `outlet_id` — select from branch-scoped outlet list
- `visited_at` — datetime picker
- `outlet_condition` — buka / tutup / order_by_wa
- `order_amount` and `receivable_amount` (sales visits)
- `po_amount` and `payment_amount` (SMD visits)
- `activities[]` (SMD visits) — checkbox group
- `notes` — textarea

### Read-only fields (not editable)

- GPS coordinates (latitude, longitude)
- Visit photo
- Display photos (SMD)
- User who performed the visit

### Validation

- Uses `UpdateVisitRequest` form request
- Authorization: admin_pusat can edit any visit, supervisor can edit only visits in their branch
- Conditional rules per visit_type (sales vs smd)
- outlet_id must belong to the user's branch (non-admin)
- visited_at must not be in the future
- SMD activities: at least one required, po_amount required if ambil_po, payment_amount required if ambil_tagihan

### Delete behavior

- Hard delete with DB transaction
- Cascade deletes: salesDetail, smdDetail, smdActivities, displayPhotos
- Physical photo files deleted from storage (visit photo, display photos)
- Redirect to visit history index with success toast

## Duplicate Outlet Detection and Merge

- `GET /outlets/duplicates` — detection page (admin_pusat and supervisor)
- `GET /outlets/duplicates/{outlet}` — comparison page showing outlet vs its duplicates
- `POST /outlets/merge` — execute merge

### Detection criteria

- Outlets with identical names (case-insensitive, trimmed) in the same branch
- Outlets with identical `official_kode` in the same branch

### Merge behavior

- User selects one outlet as "master" and one or more as duplicates
- All visits from duplicate outlets are transferred to the master outlet (UPDATE visits SET outlet_id = master)
- Audit logs (outlet_status_histories, outlet_verification_logs) are transferred to master
- Duplicate outlets are hard deleted after visit transfer
- Master outlet data (name, address, status, official_kode) is preserved unchanged

## Reports

- Accessible only for `admin_pusat` and `supervisor`
- `sales` and `smd` do not see the report menu and cannot access report routes directly
