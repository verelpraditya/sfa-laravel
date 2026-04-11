# 08 API and AJAX

- Status: Draft
- Last updated: 2026-04-11
- Purpose: Define lightweight JSON endpoints used by Blade and Alpine.

## Principles

- Keep endpoints small and focused.
- Use them for search, dependent UI, and lightweight dashboard refreshes.
- Respect role and branch scoping in every query.

## Planned Endpoints

### `GET /ajax/outlets/search`

- Query params:
  - `q`
- Behavior:
  - search by outlet `name` or `official_kode`
  - limit to current user's branch unless admin
  - return top matches only

### Example response

```json
{
  "data": [
    {
      "id": 12,
      "name": "Salon Cantik",
      "official_kode": "OFF-001",
      "district": "Cicendo",
      "city": "Bandung",
      "category": "salon",
      "outlet_status": "active"
    }
  ]
}
```

### Future candidates

- `GET /ajax/dashboard/supervisor`
- `GET /ajax/outlets/check-duplicate`
- `GET /ajax/outlets/{id}`

### `GET /visit-history` (JSON mode)

- Triggered when request sends `Accept: application/json` header
- Query params: `from`, `to`, `type`, `condition`, `search`, `page`
- Returns paginated visit data formatted for mobile card rendering
- Respects role and branch scoping (same as HTML mode)

### Example response

```json
{
  "data": [
    {
      "id": 1,
      "visited_at_formatted": "10 Apr 09:30",
      "user_name": "Budi",
      "outlet_name": "Toko Makmur",
      "visit_type": "sales",
      "outlet_condition": "buka",
      "sales_amount": 500000,
      "collection_amount": 200000,
      "can_edit": false,
      "url_show": "/visit-history/1",
      "url_edit": null,
      "url_destroy": null
    }
  ],
  "meta": {
    "current_page": 2,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### `GET /outlets/{outlet}` (JSON mode)

- Triggered when request sends `Accept: application/json` header
- Query params: `page`
- Returns paginated visit timeline for the outlet, formatted for mobile card rendering
- Visit list is scoped per user for sales/SMD roles (own visits only), full list for supervisor/admin
- KPI stats remain outlet-wide (all users)

### Example response

```json
{
  "data": [
    {
      "id": 5,
      "visited_at_formatted": "10 Apr 09:30",
      "visited_at_full": "10 Apr 2026 09:30",
      "user_name": "Budi",
      "visit_type": "sales",
      "sales_amount": 500000,
      "collection_amount": 200000,
      "url_show": "/visit-history/5"
    }
  ],
  "meta": {
    "current_page": 2,
    "last_page": 5,
    "per_page": 10,
    "total": 48
  }
}
```

## Frontend Behavior

- Use debounce for autocomplete.
- Cancel or ignore stale responses when input changes.
- Show empty-state helper when no result is found.
- Allow switching into inline create-outlet mode.

## Current Implementation Note

- The codebase currently exposes outlet autocomplete at `GET /outlets/search` with route name `ajax.outlets.search`.
- Response remains JSON and already respects branch scoping for non-admin users.
- The same endpoint is now reused by the sales visit form for live outlet search.
- Current response includes `outlet_status` instead of legacy `outlet_type` or `verification_status` fields.
