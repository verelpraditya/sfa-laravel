# 06 Pages and Forms

- Status: Draft
- Last updated: 2026-03-28
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
- NOO list
- Reports

## Login Page

- Uses `username` and `password`
- Has remember-me option
- Supports show/hide password interaction
- Styled for mobile-first field usage

## Shared Outlet Search Component

- Search by outlet name or `official_kode`
- AJAX autocomplete without page reload
- Show limited result list
- Allow inline new outlet form when not found

## Outlet Module Pages

- `GET /outlets`
  - desktop table view
  - mobile card view
  - filter by search, verification status, and outlet type
- `GET /outlets/create`
  - create master outlet form
- `GET /outlets/{outlet}/edit`
  - edit master outlet form

## Outlet Verification Pages

- `GET /outlet-verifications`
  - pending/verified outlet review list
- `GET /outlet-verifications/{outlet}/edit`
  - verification form for status and official code

## Sales Visit Form

### Fields

- `outlet_id` or inline new outlet fields
- `name` when new
- `address` when new
- `district` when new
- `city` when new
- `category` when new
- `outlet_type` when new or updating status
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
- new inline outlets default to `active`
- new `prospek` outlets keep verification status empty/null

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
- `display_photo`
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
- `display_photo` required if `merapikan_display`
- new inline outlets default to `active`

### Implemented pages

- `GET /smd-visits`
- `GET /smd-visits/create`
- `POST /smd-visits`

## Supervisor Visit Entry

- No custom supervisor-only form.
- Supervisor selects visit type first.
- Selected type loads the same validation rules as the target form.
