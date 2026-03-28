# 01 Requirements

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Business requirements, role behavior, and acceptance rules.

## Product Goal

Build a web-based SFA for distributor operations with these priorities:

- Visit reporting for field teams.
- Outlet master growth directly from field visits.
- Multi-branch visibility and monitoring.
- Supervisor verification of newly created outlets and official codes.

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

## Branch Rules

- Every `sales`, `smd`, and `supervisor` belongs to one branch.
- New outlets created during visits are automatically assigned to the creator's branch.
- Non-admin users can only search and access outlets from their own branch.
- `admin_pusat` can view all branches.

## Outlet Rules

- Outlet can be selected from existing data or created during a visit.
- Outlet master should also be manageable from a dedicated outlet module.
- Minimum outlet fields:
  - `name`
  - `address`
  - `district`
  - `city`
  - `category`
  - `outlet_type`
- Categories:
  - `salon`
  - `toko`
  - `barbershop`
  - `lainnya`
- Outlet types:
  - `prospek`
  - `noo`
  - `pelanggan_lama`
- `official_kode` is the official store code term used in the app.
- If `outlet_type = pelanggan_lama`, `official_kode` is required.
- If `outlet_type = prospek`, outlet enters prospect follow-up list and does not require verification status.
- If `outlet_type = noo`, supervisor later fills `official_kode` when available.
- Sales can change outlet status from `prospek` to `noo` on later visits.
- Outlet operational status is separate from type:
  - `active`
  - `inactive`
- Outlet search should support branch-scoped autocomplete by `name` and `official_kode`.

## Sales Visit Requirements

Sales can only access the sales visit form.

### Required flow

- Choose existing outlet or create a new one.
- If new, fill outlet address and type details.
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

## Current Implementation Note

- Sales visit form now exists with branch-scoped outlet autocomplete and inline new-outlet creation.

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

## Current Implementation Note

- SMD visit form now exists with multiple activity selection, conditional validation, and outlet autocomplete.
- `Prospek` no longer appears as pending verification by default.

## Supervisor Rules

- Supervisor can view only branch activity for their own branch.
- Supervisor can verify outlets.
- Supervisor can fill and update `official_kode`.
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

## Acceptance Criteria Snapshot

- Sales can search outlets without page reload.
- New outlet created during visit is immediately reusable in later visits.
- Supervisor can verify pending outlets and fill `official_kode`.
- Submitted visits remain immutable for supervisors.
- Desktop list views support tables.
- Mobile list views support cards.
- Mobile-first form UX supports field use on phones.
