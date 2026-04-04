# 01 Requirements

- Status: Draft
- Last updated: 2026-03-31
- Purpose: Business requirements, role behavior, and acceptance rules.

## Product Goal

Build a web-based SFA for distributor operations with these priorities:

- Visit reporting for field teams.
- Outlet master growth directly from field visits.
- Multi-branch visibility and monitoring.
- Supervisor activation of pending outlets through `official_kode`.

## Roles

- `admin_pusat`
- `supervisor`
- `sales`
- `smd`

## Auth Requirements

- Authentication uses username and password.
- Public registration is disabled.
- Accounts are created internally by admin workflows.
- Only active users can log in.
- Public root path redirects directly to login.

## Branch Rules

- Every `sales`, `smd`, and `supervisor` belongs to one branch.
- Every branch stores its own timezone, e.g. `Asia/Jakarta` or `Asia/Makassar`.
- New outlets created during visits are automatically assigned to the creator's branch.
- Non-admin users can only search and access outlets from their own branch.
- `admin_pusat` can view all branches.

## Operational Lists

- `Prospek Follow Up` should show prospect outlets for sales/supervisor follow-up.
- `Verifikasi Outlet` should show only pending outlets waiting for `official_kode`.
- `Outlet Inactive` should show outlets marked inactive for monitoring and review.

## Outlet Rules

- Outlet can be selected from existing data or created during a visit.
- Outlet master should also be manageable from a dedicated outlet module.
- Minimum outlet fields:
  - `name`
  - `address`
  - `district`
  - `city`
  - `category`
  - `outlet_status`
- Categories:
  - `salon`
  - `toko`
  - `barbershop`
  - `lainnya`
- Outlet statuses:
  - `prospek`
  - `pending`
  - `active`
  - `inactive`
- `official_kode` is the official store code term used in the app.
- If sales creates a new inline outlet as `Prospek`, it saves as `outlet_status = prospek`.
- If sales creates a new inline outlet as `NOO`, it saves as `outlet_status = pending`.
- If sales creates a new inline outlet as `Pelanggan Lama`, it saves as `outlet_status = active` and requires `official_kode`.
- Supervisor later fills `official_kode` for `pending` outlets and activates them.
- Outlet search should support branch-scoped autocomplete by `name` and `official_kode`.

## Sales Visit Requirements

Sales can only access the sales visit form.

### Required flow

- Choose existing outlet or create a new one.
- If new, fill outlet address and initial business choice:
  - `Prospek`
  - `NOO`
  - `Pelanggan Lama`
- Set outlet condition:
  - `buka`
  - `tutup`
- Capture mandatory evidence:
  - GPS
  - timestamp
  - visit proof photo

### Conditional fields

- If `kondisi_outlet = buka`:
  - `nominal_order` optional
  - `total_tagihan` optional
- If `kondisi_outlet = tutup`:
  - order and receivable inputs are hidden or disabled

### Current implementation note

- Sales visit form now exists with branch-scoped outlet autocomplete and inline new-outlet creation.
- Mobile photo flow prefers camera capture, compresses the image client-side, and shows a preview before submit.
- Upload filenames are normalized to a readable `username_outlet_YYYYMMDD_HHMMSS` format.
- Validation messages are customized in Indonesian and non-file fields are preserved after validation errors.

## SMD Visit Requirements

SMD can only access the SMD visit form.

### Required flow

- Choose existing outlet or create a new one.
- Choose one or more activities:
  - `ambil_po`
  - `merapikan_display`
  - `tukar_faktur`
  - `ambil_tagihan`
- Capture mandatory evidence:
  - GPS
  - timestamp
  - visit proof photo

### Conditional fields

- If `ambil_po` is selected, `nominal_po` is required.
- If `ambil_tagihan` is selected, `nominal_pembayaran` is required.
- If `merapikan_display` is selected, `foto_display` is required.

### Current implementation note

- SMD visit form now exists with multiple activity selection, conditional validation, and outlet autocomplete.
- SMD inline outlet creation uses the same outlet status mapping as sales visit.
- Mobile photo flow matches sales visit form: camera hint, preview, and client-side compression.

## Supervisor Rules

- Supervisor can view only branch activity for their own branch.
- Supervisor can activate pending outlets.
- Supervisor fills `official_kode` for pending outlets and cannot edit submitted visit evidence.
- Supervisor cannot edit or delete submitted visits.
- Supervisor can create visits only for themself, not on behalf of others.
- Supervisor can choose visit form type:
  - `sales`
  - `smd`

## Dashboard Rules

- `admin_pusat`: all-branch monitoring.
- `supervisor`: branch dashboard plus personal activity view.
- `sales`: personal dashboard only.
- `smd`: personal dashboard only.
- `admin_pusat` and `supervisor` can access reports.
- `sales` and `smd` should not see or access the report module.

## Acceptance Criteria Snapshot

- Sales can search outlets without page reload.
- New outlet created during visit is immediately reusable in later visits.
- Supervisor can activate pending outlets by filling `official_kode`.
- Submitted visits remain immutable for supervisors.
- Desktop list views support tables.
- Mobile list views support cards.
- Mobile-first form UX supports field use on phones.
