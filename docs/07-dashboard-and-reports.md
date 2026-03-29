# 07 Dashboard and Reports

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Dashboard content, reporting direction, and chart usage.

## Dashboard Pattern

- Top metric cards for quick scanning.
- Charts in the middle section.
- Recent activity and pending actions below.
- Desktop favors tables for dense data.
- Mobile favors cards for readability.

## Current Implementation Note

- Dashboard cards and trend chart now pull real aggregates from `outlets` and `visits` based on role scope.
- Pending verification metrics exclude `prospek` outlets.
- Dashboard also tracks inactive outlets.
- Phase 2 dashboard refinement adds deeper role-based operational metrics, with emphasis on visit tim hari ini, sales amount hari ini, collection hari ini, jumlah PO hari ini, and monitoring kunjungan.
- Dashboard trend chart now compares `Kunjungan` and `Collection` in one chart using dual axes, where `Collection` combines sales receivable input and SMD payment collection input.
- `Sales Amount Hari Ini` on supervisor/admin combines sales order and SMD PO for the same day.
- Dashboard visit table is limited to today's latest 10 visits in the current role scope.
- Full visit history is moved to a dedicated `History Kunjungan` page with detail view.

## Charts

- Library: `Chart.js`
- Typical chart types:
  - bar
  - line
  - doughnut

## Admin Pusat Dashboard

- Total visits across branches
- Branch ranking
- Visit trends by period
- Outlet growth by branch
- Pending verifications
- `NOO` without `official_kode`

## Supervisor Dashboard

- Total branch visits today
- Sales performance in branch
- SMD performance in branch
- Pending outlet verification count
- Prospect follow-up list
- `NOO` pending `official_kode`
- Personal activity section

### Current implementation

- Supervisor dashboard now has two views in one account:
  - `Dashboard Cabang`
  - `Aktivitas Saya`
- `Dashboard Cabang` shows branch-scoped aggregates and recent team activity.
- `Aktivitas Saya` shows only visits performed by the supervisor user.

## Sales Dashboard

- Personal visits today
- Recent visit history
- Outlet count created by self
- Order and receivable totals
- Open vs closed outlet visit breakdown

## SMD Dashboard

- Personal activity summary
- Count of PO collection activities
- Count or nominal of payment collection
- Display activity summary
- Recent visit history

## Reports

- Visits by branch
- Visits by user
- Outlet growth by period
- Prospect follow-up report
- `NOO` pending official code report
- Sales monetary recap
- SMD activity recap

## Current reporting implementation

- Report module now provides role-aware views for:
  - `sales`
  - `smd`
  - `outlets`
- CSV export is available from the report page using the current date filters.
