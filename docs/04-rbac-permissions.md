# 04 RBAC Permissions

- Status: Draft
- Last updated: 2026-04-10
- Purpose: Role-based access control matrix and scoping rules.

## Role Matrix

| Capability | Admin Pusat | Supervisor | Sales | SMD |
|---|---|---|---|---|
| View all branches | Yes | No | No | No |
| View own branch only | Yes | Yes | Yes | Yes |
| Manage branches | Yes | No | No | No |
| Manage users | Yes | No | No | No |
| View all outlets | Yes | No | No | No |
| View branch outlets | Yes | Yes | Yes | Yes |
| View outlet detail | Yes | Yes (own branch) | Yes (own branch) | Yes (own branch) |
| Manage branch outlets | Yes | Yes | No | No |
| Delete outlet (no visits) | Yes | No | No | No |
| Create outlet during visit | Yes | Yes | Yes | Yes |
| Activate pending outlet | Yes | Yes | No | No |
| Fill `official_kode` | Yes | Yes | No | No |
| View reports | Yes | Yes | No | No |
| Create sales visit | Optional later | Yes | Yes | No |
| Create SMD visit | Optional later | Yes | No | Yes |
| Edit submitted visit | Yes | Yes (own branch) | No | No |
| Delete submitted visit | Yes | Yes (own branch) | No | No |
| Detect duplicate outlets | Yes | Yes (own branch) | No | No |
| Merge duplicate outlets | Yes | Yes (own branch) | No | No |
| View branch dashboard | Yes | Yes | No | No |
| View personal dashboard | Yes | Yes | Yes | Yes |

## Named Permission Methods

The `User` model exposes convenience methods used in Blade templates and controllers:

| Method | Allowed Roles | Notes |
|---|---|---|
| `canDeleteOutlets()` | admin_pusat | Hard delete only when outlet has no linked visits |
| `canMergeOutlets()` | admin_pusat, supervisor | Supervisor scoped to own branch |

## Branch Scope Rules

- `sales`, `smd`, and `supervisor` can only access data from their own branch.
- Outlet autocomplete for non-admin users is limited to the user's branch.
- New outlets inherit the creator's branch.
- Supervisor visit creation is always under the supervisor's own account.

## Visit Scope Rules

- `sales` sees only sales visit entry.
- `smd` sees only SMD visit entry.
- `supervisor` can choose sales or SMD visit type.
- Supervisor cannot submit visits on behalf of someone else.

## Governance Rules

- Supervisor activates pending outlet records, not visit evidence.
- `official_kode` updates are controlled by supervisor/admin.
- Submitted visits can be edited or deleted by `admin_pusat` and `supervisor` (own branch only for supervisor). Photos and GPS coordinates remain immutable — only business data fields (outlet, date, condition, amounts, activities, notes) are editable.
- Outlet deletion is restricted to `admin_pusat` and only allowed when the outlet has no linked visits.
- Duplicate outlet detection and merging is available to `admin_pusat` and `supervisor` (own branch). Merging transfers all visits from duplicate outlets to the selected master outlet before deleting the duplicates.
- Public user self-registration is disabled.

## Current Code Foundation

- `role` middleware alias is available for route protection.
- Workspace routes exist for `admin_pusat`, `supervisor`, `sales`, and `smd` as access groundwork.
- Outlet module already scopes list and search results to the current branch for non-admin users.
- Supervisor/admin verification routes now exist for reviewing pending outlets and assigning official codes.
- Report routes and report navigation are restricted to `admin_pusat` and `supervisor`.
- Outlet master create/edit is restricted to `admin_pusat` and `supervisor`; `sales` and `smd` can still create outlets inline during visits only.
- Branch and user master CRUD are restricted to `admin_pusat`.
