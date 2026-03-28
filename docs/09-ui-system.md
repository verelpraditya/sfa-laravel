# 09 UI System

- Status: Draft
- Last updated: 2026-03-28
- Purpose: Visual direction, component behavior, and responsive rules.

## Design Direction

- Light clean corporate UI
- Modern but safe for outdoor use by field teams
- Rounded cards, soft borders, calm shadows
- Clean spacing and legible typography

## Typography

- Font family: `Inter`
- Source: `fonts.bunny.net`
- Prioritize readability on mobile devices

## Layout Rules

- Mobile-first by default
- Form pages prioritize one-column flow
- Desktop reporting pages can use wider layouts and tables
- Avoid overly dense controls on mobile

## Navigation Rules

- Desktop: fixed or sticky sidebar
- Mobile: hamburger button with offcanvas menu

## List and Table Rules

- Desktop: tables for reports and master data lists
- Mobile: cards or stacked list items

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
