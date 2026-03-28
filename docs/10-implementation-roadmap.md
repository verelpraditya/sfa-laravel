# 10 Implementation Roadmap

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Phased plan for building the MVP.

## Phase 1: Foundation

- Finalize environment target and Laravel version
- Scaffold Laravel app
- Set up auth foundation
- Set up Tailwind, Alpine, Chart.js, Bunny font
- Build documentation discipline into workflow

### Current progress

- Done: Laravel 12 scaffold
- Done: docs structure and initial drafts
- Done: frontend package direction and base welcome screen
- Done: `npm install`, production asset build, and baseline test pass
- Done: Breeze Blade auth with username login and disabled public registration
- Done: branch and role groundwork in user schema plus demo seed users
- Done: core SFA schema migrations and role middleware groundwork
- Done: branch-scoped outlet module and AJAX search foundation
- Done: sales visit workflow foundation with inline outlet creation and history page
- Done: SMD visit workflow foundation with multi-activity validation and history page
- Done: supervisor verification screens and dashboard real aggregates
- Done: revised outlet lifecycle with nullable verification for prospek and inactive outlet status
- Done: branch master with timezone support for local operational time display
- Done: admin-only user master with role and branch assignment
- Done: operational outlet lists for prospek, NOO, and inactive outlets
- Done: basic report pages and CSV export foundation
- Next: deeper branch-scoped reporting details and dashboard polish

## Demo Seed Accounts

- `adminpusat` / `password`
- `supervisorbdg` / `password`
- `salesbdg` / `password`
- `smdbdg` / `password`

## Phase 2: Access and Master Data

- Branch management
- User management
- Role and branch scoping
- Outlet master and verification status

## Phase 3: Visit Workflows

- Outlet autocomplete
- Sales visit flow
- SMD visit flow
- Evidence upload
- Prospect and NOO behavior

## Phase 4: Verification and Monitoring

- Supervisor outlet verification
- `official_kode` workflow
- Dashboards by role
- Basic reports and export

## Phase 5: Data Growth and Optimization

- Duplicate outlet assistance
- Import from Accurate export file
- Performance improvements
- Additional audit and reporting enhancements
