# 06 Pages and Forms

- Status: Draft
- Last updated: 2026-04-01
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
- `GET /outlets/create`
  - create master outlet form
- `GET /outlets/{outlet}/edit`
  - edit master outlet form

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
- financial inputs only when `outlet_condition = buka`
- new inline outlets are mapped to outlet status:
  - `prospek` -> `prospek`
  - `noo` -> `pending`
  - `pelanggan_lama` -> `active`
- mobile photo flow prefers camera capture via browser hint, shows preview, and compresses image client-side before upload
- uploaded photo filename is normalized to `username_outlet_YYYYMMDD_HHMMSS`
- validation messages are customized in Indonesian and old input is preserved after submit failure

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

## Visit Detail

- Visit detail page shows:
  - visit summary
  - latitude and longitude
  - visit proof photo
  - SMD display photo gallery when available
  - embedded OpenStreetMap based on saved coordinates
  - shortcut link to Google Maps

## Reports

- Accessible only for `admin_pusat` and `supervisor`
- `sales` and `smd` do not see the report menu and cannot access report routes directly
