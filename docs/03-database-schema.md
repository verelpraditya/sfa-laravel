# 03 Database Schema

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Core schema draft, tables, relationships, enums, and constraints.

## Core Tables

### `branches`

- `id`
- `code`
- `name`
- `city`
- `timezone`
- `address` nullable
- `is_active`
- timestamps

### `users`

- `id`
- `branch_id` nullable for `admin_pusat`
- `name`
- `username` unique
- `email` nullable unique
- `password`
- `role` = `admin_pusat|supervisor|sales|smd`
- `phone` nullable
- `is_active`
- `last_login_at` nullable
- timestamps

### `outlets`

- `id`
- `branch_id`
- `name`
- `address`
- `district`
- `city`
- `category` = `salon|toko|barbershop|lainnya`
- `outlet_type` = `prospek|noo|pelanggan_lama`
- `outlet_status` = `active|inactive`
- `official_kode` nullable unique
- `verification_status` = `pending|verified` nullable
- `verified_by` nullable
- `verified_at` nullable
- `created_by`
- `updated_by` nullable
- timestamps

### `visits`

- `id`
- `branch_id`
- `outlet_id`
- `user_id`
- `visit_type` = `sales|smd`
- `outlet_condition` = `buka|tutup` nullable
- `latitude`
- `longitude`
- `visit_photo_path`
- `visited_at`
- `notes` nullable
- timestamps

### `sales_visit_details`

- `id`
- `visit_id`
- `order_amount` nullable
- `receivable_amount` nullable
- timestamps

### `smd_visit_details`

- `id`
- `visit_id`
- `po_amount` nullable
- `payment_amount` nullable
- `display_photo_path` nullable
- timestamps

### `smd_visit_activities`

- `id`
- `visit_id`
- `activity_type` = `ambil_po|merapikan_display|tukar_faktur|ambil_tagihan`
- timestamps

## Implemented Status

- `branches`, `users`, `outlets`, `visits`, `sales_visit_details`, `smd_visit_details`, `smd_visit_activities`, `outlet_status_histories`, and `outlet_verification_logs` migrations are already created in the codebase.

## Optional Audit Tables

### `outlet_status_histories`

- `id`
- `outlet_id`
- `old_outlet_type` nullable
- `new_outlet_type`
- `changed_by`
- `notes` nullable
- `created_at`

### `outlet_verification_logs`

- `id`
- `outlet_id`
- `verification_status`
- `official_kode` nullable
- `verified_by`
- `notes` nullable
- `created_at`

## Key Relationships

- `branches` has many `users`
- `branches` has many `outlets`
- `branches` has many `visits`
- `users` has many `visits`
- `outlets` has many `visits`
- `visits` has one `sales_visit_details`
- `visits` has one `smd_visit_details`
- `visits` has many `smd_visit_activities`

## Important Constraints

- `official_kode` must be unique when present.
- `username` must be unique.
- `pelanggan_lama` requires `official_kode`.
- `prospek` can keep `verification_status = null`.
- `sales` can create only `sales` visits.
- `smd` can create only `smd` visits.
- `supervisor` can create both visit types, only for self.
- Submitted visits should be treated as immutable business records.

## Suggested Indexes

- `outlets(branch_id, name)`
- `outlets(official_kode)`
- `visits(branch_id, visited_at)`
- `visits(user_id, visited_at)`
- `visits(outlet_id, visited_at)`
