# 07 Dashboard and Reports

- Status: Draft
- Last updated: 2026-04-01
- Purpose: Dashboard content, reporting direction, and chart usage.

## Dashboard Pattern

- Top metric cards for quick scanning.
- Charts in the middle section.
- Recent activity and operational actions below.
- Desktop favors tables for dense data.
- Mobile favors cards for readability.

## Current Implementation Note

- Dashboard cards and trend chart now pull real aggregates from `outlets` and `visits` based on role scope.
- Outlet monitoring now uses unified `outlet_status` values: `prospek`, `pending`, `active`, `inactive`.
- Dashboard also tracks inactive outlets.
- Phase 2 dashboard refinement adds deeper role-based operational metrics, with emphasis on `visit tim hari ini`, `sales amount hari ini`, `collection hari ini`, and monitoring kunjungan.
- Dashboard trend chart compares `Kunjungan` and `Collection` in one chart using dual axes, where `Collection` combines sales receivable input and SMD payment collection input.
- `Sales Amount Hari Ini` on supervisor/admin combines sales order and SMD PO for the same day.
- Dashboard visit table is limited to today's latest 10 visits in the current role scope.
- Full visit history is moved to a dedicated `History Kunjungan` page with detail view.
- Supervisor/admin dashboard now includes customer insight blocks for top customers and customers inactive for more than 30 days.

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
- Pending outlet count
- Top customer and stale-customer monitoring

## Supervisor Dashboard

- Total branch visits today
- Sales performance in branch
- SMD performance in branch
- Pending outlet activation count
- Prospect follow-up list
- Personal activity section

### Prospect follow-up context

- Prospect list now includes basic follow-up context:
  - `Terakhir Dikunjungi`
  - `User Terakhir`
  - simple aging text such as `Hari ini` or `X hari lalu`
- This is intended as lightweight operational context for sales and supervisor follow-up without introducing a separate prospect scoring workflow yet.

### Current implementation

- Supervisor dashboard has two views in one account:
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
- Pending official code report
- Sales monetary recap
- SMD activity recap

## Current Reporting Implementation

- Report module now provides role-aware views for:
  - `sales`
  - `smd`
  - `outlets`
- CSV export is available from the report page using the current date filters.
- Report navigation and routes are restricted to `admin_pusat` and `supervisor`.
