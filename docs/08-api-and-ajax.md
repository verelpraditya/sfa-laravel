# 08 API and AJAX

- Status: Draft
- Last updated: 2026-03-28
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
      "outlet_type": "pelanggan_lama"
    }
  ]
}
```

### Future candidates

- `GET /ajax/dashboard/supervisor`
- `GET /ajax/outlets/check-duplicate`
- `GET /ajax/outlets/{id}`

## Frontend Behavior

- Use debounce for autocomplete.
- Cancel or ignore stale responses when input changes.
- Show empty-state helper when no result is found.
- Allow switching into inline create-outlet mode.

## Current Implementation Note

- The codebase currently exposes outlet autocomplete at `GET /outlets/search` with route name `ajax.outlets.search`.
- Response remains JSON and already respects branch scoping for non-admin users.
