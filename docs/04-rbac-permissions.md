# 04 RBAC Permissions

- Status: Draft
- Last updated: 2026-03-28
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
| Manage branch outlets | Yes | Yes | No | No |
| Create outlet during visit | Yes | Yes | Yes | Yes |
| Verify outlet | Yes | Yes | No | No |
| Fill `official_kode` | Yes | Yes | No | No |
| Create sales visit | Optional later | Yes | Yes | No |
| Create SMD visit | Optional later | Yes | No | Yes |
| Edit submitted visit | No | No | No | No |
| Delete submitted visit | No | No | No | No |
| View branch dashboard | Yes | Yes | No | No |
| View personal dashboard | Yes | Yes | Yes | Yes |

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

- Supervisor verifies outlet records, not visit evidence.
- `official_kode` updates are controlled by supervisor/admin.
- Submitted visits remain immutable for audit safety.
- Public user self-registration is disabled.

## Current Code Foundation

- `role` middleware alias is available for route protection.
- Workspace routes exist for `admin_pusat`, `supervisor`, `sales`, and `smd` as access groundwork.
- Outlet module already scopes list and search results to the current branch for non-admin users.
- Supervisor/admin verification routes now exist for reviewing pending outlets and assigning official codes.
- Outlet master create/edit is restricted to `admin_pusat` and `supervisor`; `sales` and `smd` can still create outlets inline during visits only.
