# 12 Open Questions

- Status: Active
- Last updated: 2026-03-28
- Purpose: Capture unresolved choices and risks.

## Pending Decision

- Confirm which PHP `8.2+` CLI path should be the standard local command path: Herd or explicit binary.
- Confirm database driver for local development: SQLite or MySQL.
- Decide when to replace demo seed accounts with admin-managed user creation flow.

## Risks

- Shell using PHP `7.3` by default can break Artisan/Composer commands if not overridden.
- Photo uploads from mobile need size control and storage planning.
- Duplicate outlet creation needs careful UX and validation support.

## Follow-up Notes

- Hosting target is confirmed as PHP `8.2+`.
- Modern Laravel foundation is already in place.
- Next implementation pass should standardize the local PHP 8.2 command path.
- Local development is currently aligned to MySQL, not SQLite.
- Next major build target is outlet management plus visit entry workflows with branch scoping.
- Outlet module is in place, so the next major build target is the actual sales visit form and its inline create-outlet workflow.
