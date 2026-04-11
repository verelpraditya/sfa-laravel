# 09 UI System

- Status: Draft
- Last updated: 2026-04-11
- Purpose: Visual direction, component behavior, and responsive rules.

## Design Direction

- Clean-but-colorful corporate UI — migrated from glassmorphism to solid, readable surfaces
- Modern but safe for outdoor use by field teams
- Rounded cards, soft borders, calm shadows
- Clean spacing and legible typography
- Color is used meaningfully: status badges, accent bars on KPI cards, gradient hero banners — not rainbow grids
- Primary palette uses `sky + indigo + violet` gradients for hero sections
- CTA buttons use solid `sky-500` (no glass/blur)
- Toast notifications use dark pill style (`bg-slate-900`, rounded-full) at bottom-center
- Sidebar remains dark theme (navy/sky gradient with grid glow)

## Typography

- Font family: `Inter`
- Source: `fonts.bunny.net`
- Prioritize readability on mobile devices

## Design System Components

### Page Headers
- Gradient hero banner using `app-hero-gradient` class (sky → indigo → violet)
- White text, breadcrumb badges in `bg-white/20`, subtitle in `text-white/70`
- Action buttons use `bg-white/15` with hover `bg-white/25`

### KPI Cards
- Colored variants with left accent bar: `app-kpi-blue`, `app-kpi-sky`, `app-kpi-emerald`, `app-kpi-amber`, `app-kpi-violet`, `app-kpi-rose`
- Each has a `::before` pseudo-element for the left border accent
- Gradient background from tinted to white (`bg-gradient-to-br from-{color}-50 to-white`)

### Badges
- `app-badge` base class with color variants: `app-badge-sky`, `app-badge-emerald`, `app-badge-amber`, `app-badge-rose`, `app-badge-violet`, `app-badge-slate`
- Used for status indicators, type labels, category markers

### Info Detail Pages
- List rows with icons (not tiled colored cards)
- Each row: icon container (colored bg) + label (small uppercase) + value
- Divided by `divide-y divide-slate-50`

### Buttons
- `app-action-primary` — sky-500 solid, main CTA
- `app-action-secondary` — outlined with border-2
- `app-action-danger` — rose-500 solid for destructive actions
- `app-btn-sm` / `app-btn-sm-primary` — compact table/card action buttons

### Toast Notifications
- Dark pill style: `bg-slate-900 rounded-full` fixed at bottom-center
- Auto-dismiss after 4 seconds using Alpine.js

### Panels
- `app-panel` — white card with border and shadow
- `app-soft-panel` — subtle slate-50 background
- Top accent bar on key panels using `app-hero-gradient` with `h-1`

## Sidebar Navigation

- Dark theme with navy gradient background
- Grid glow decorative pattern
- Collapsible menu groups with Alpine.js for organized navigation
- Menu groups auto-expand when a child item is active
- Desktop: fixed left sidebar (22rem width)
- Mobile: offcanvas slide-in with backdrop blur

## Layout Rules

- Mobile-first by default
- Form pages prioritize one-column flow
- Desktop reporting pages can use wider layouts and tables
- Avoid overly dense controls on mobile

## Navigation Rules

- Desktop: sidebar navigation
- Mobile: hamburger button with offcanvas menu

## List and Table Rules

- Desktop: tables for reports and master data lists
- Mobile: cards or stacked list items
- Visit history mobile uses infinite scroll (IntersectionObserver + JSON fetch) instead of traditional pagination
- Page 1 data is embedded server-side for instant render, subsequent pages fetched on demand
- Loading spinner shown during fetch, end-of-list indicator when all data loaded

## Interaction Rules

- Alpine drives:
  - autocomplete
  - dropdowns
  - tabs
  - conditional sections
  - image preview
  - GPS capture states
- Keep animations subtle and functional

## Component Priorities

- App shell
- Sidebar/offcanvas nav
- Stat cards
- Table component
- Mobile data cards
- Outlet autocomplete field
- Conditional form sections
- Photo upload preview
- Status badges
- Empty states and loading states

## Current UI Seed

- The default welcome page has been replaced with a branded project landing screen.
- The landing screen already reflects the agreed direction: light theme, rounded cards, Inter typography, and a mobile-friendly composition.
- Breeze auth pages have been restyled to match the same modern, light, mobile-first direction.
- App shell now uses a desktop sidebar and mobile offcanvas navigation with a navy/sky visual system.
- All major pages have been migrated from glassmorphism to the clean-colorful design system.
- Dashboard, visit history, visit detail, outlet pages, reports, branches, users, and auth pages all follow the new design system.
- Sales visit form (`sales-visits/create`) and SMD visit form (`smd-visits/create`) were intentionally left unchanged — user is satisfied with their current design.
