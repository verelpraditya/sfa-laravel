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
