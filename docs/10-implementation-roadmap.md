# 10 Implementation Roadmap

- Status: Draft
- Last updated: 2026-04-01
- Purpose: Phased plan for building the MVP.

## Phase 1: Core Workflow Foundation

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
- Done: unified outlet lifecycle using `outlet_status = prospek|pending|active|inactive`
- Done: branch master with timezone support for local operational time display
- Done: admin-only user master with role and branch assignment
- Done: operational outlet lists for prospek, pending, and inactive outlets
- Done: basic report pages and CSV export foundation
- Done: mobile-first redesign for shell, dashboard, reports, visit history, and field visit forms
- Done: report access restricted to `admin_pusat` and `supervisor`
- Done: camera-preferred upload flow with client-side compression and preview for visit forms
- Done: duplicate submit protection for sales and SMD visit forms using submission tokens and locked submit buttons
- Done: SMD display-photo workflow upgraded to support 1-10 photos with camera-first per-photo capture UX
- Done: visit detail page now includes embedded map location and Google Maps shortcut
- Done: prospect operational list now shows last-visit follow-up context
- Phase 1 status: functionally complete

## Phase 2: Monitoring, Reporting, and Data Quality

- Dashboard refinement per role
- Branch/user-aware reporting filters
- Follow-up and aging indicators for prospek and pending outlets
- Better monitoring for inactive outlets and pending activation
- Data quality support and duplicate handling assistance

## Phase 3: Integration, Automation, and Scale

- Import from Accurate export file
- Merge and deduplicate outlet data
- Advanced audit trail and follow-up workflow
- Performance improvements and storage hardening
- Additional automation and reminder support

## Demo Seed Accounts

- `adminpusat` / `password`
- `supervisorbdg` / `password`
- `salesbdg` / `password`
- `smdbdg` / `password`
