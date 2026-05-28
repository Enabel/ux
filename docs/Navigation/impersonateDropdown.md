# ImpersonateDropdown Component

## Description

A navbar dropdown that lets `ROLE_ALLOWED_TO_SWITCH` users start impersonating someone else. A debounced search input queries a JSON endpoint provided by the host application and renders the results as clickable items that navigate to the current URL with the `?_switch_user=<email>` parameter appended — the contract Symfony's Security component listens for.

Replaces the ad-hoc impersonate widget copied across Enabel apps (SymfonyBase, InformationPortal, Impala) — same Stimulus pattern, single shipped controller.

Pairs with [`Enabel:Ux:ImpersonateBanner`](impersonateBanner.md) (issue #16), which signals the active impersonation at the top of the page.

## Parameters

| Parameter           | Type      | Description                                                                                            | Default                   |
|:--------------------|:----------|:-------------------------------------------------------------------------------------------------------|:--------------------------|
| `searchUrl`         | `string`  | URL of the project's JSON search endpoint (**required**)                                               | —                         |
| `searchPlaceholder` | `string`  | Placeholder shown in the search input — translate at the call site                                     | `''`                      |
| `noResultsLabel`    | `string`  | Label shown when the query returns no rows                                                             | `'No results'`            |
| `icon`              | `string`  | Icon identifier passed to `ux_icon()`                                                                  | `'fa6-solid:user-secret'` |
| `title`             | `?string` | Tooltip on the dropdown toggle button (HTML `title` attribute)                                         | `null`                    |
| `exitParameter`     | `string`  | Query-string key appended on row click (Symfony's default is `_switch_user`)                           | `'_switch_user'`          |
| `debounce`          | `int`     | Search debounce delay in milliseconds                                                                  | `250`                     |
| `minLength`         | `int`     | Minimum query length before the controller fires a fetch (under this, the results list is cleared)     | `2`                       |

## Endpoint contract

The component issues `GET {searchUrl}?q={query}` after the debounce window, expecting a JSON array of objects with the following shape:

```json
[
    { "email": "jane.doe@enabel.be", "displayName": "Jane Doe", "initials": "JD" },
    { "email": "john.smith@enabel.be", "displayName": "John Smith", "initials": "JS" }
]
```

- `email` — appended as `?_switch_user=<email>` on click (the only field that is functionally required for impersonation)
- `displayName` — shown as the row label; falls back to `email` if missing
- `initials` — rendered in a small round avatar to the left of the label; the avatar is omitted if `initials` is empty

The endpoint must be protected by `ROLE_ALLOWED_TO_SWITCH` (or stricter) at the controller level — the component only renders the widget, it does not gate access.

The Stimulus controller fires no request until the input contains at least `minLength` characters (default 2), aborts in-flight requests when the query changes, and renders an empty-state row when the endpoint returns `[]`.

## Usage

### Inside Enabel:Ux:Menu, gated by Symfony Security

```twig
{% component 'Enabel:Ux:Menu' with { align: 'end' } %}
    {% block content %}
        {% if is_granted('ROLE_ALLOWED_TO_SWITCH') and not is_granted('IS_IMPERSONATOR') %}
            {{ component('Enabel:Ux:ImpersonateDropdown', {
                searchUrl: path('admin_user_search'),
                searchPlaceholder: 'nav.impersonate.search'|trans,
                noResultsLabel: 'nav.impersonate.no_results'|trans,
                title: 'nav.impersonate.title'|trans,
            }) }}
        {% endif %}

        {{ component('Enabel:Ux:UserMenu', { name: app.user.displayName }) }}
    {% endblock %}
{% endcomponent %}
```

The `not is_granted('IS_IMPERSONATOR')` guard keeps the entry-point hidden once impersonation is active — the `Enabel:Ux:ImpersonateBanner` then takes over.

### Sample controller for the search endpoint

```php
#[Route('/admin/users/search', name: 'admin_user_search', methods: ['GET'])]
#[IsGranted('ROLE_ALLOWED_TO_SWITCH')]
public function search(Request $request, UserRepository $users): JsonResponse
{
    $query = trim($request->query->get('q', ''));
    if (\strlen($query) < 2) {
        return new JsonResponse([]);
    }

    return new JsonResponse(array_map(
        fn (User $user) => [
            'email' => $user->getEmail(),
            'displayName' => $user->getDisplayName(),
            'initials' => $user->getInitials(),
        ],
        $users->searchByName($query, limit: 20),
    ));
}
```

## Stimulus controller

The component is wired to the Stimulus controller shipped at `assets/dist/impersonate_dropdown.js` (`@enabel/ux/impersonate_dropdown`). Symfony AssetMapper auto-discovers it through the bundle's `assets/package.json` — no manual `controllers.json` edit needed in the consumer.

The controller is registered with `fetch: 'lazy'` — the JS file is downloaded only when an element actually carries the `data-controller` attribute, which only happens when the dropdown is rendered (i.e. for `ROLE_ALLOWED_TO_SWITCH` users).

### XSS safety

All rendered fields (`displayName`, `email`, `initials`) are written to the DOM through `textContent`, never `innerHTML`, so a hostile JSON payload from the search endpoint cannot inject markup or scripts. The clickable URL is built via `new URL(document.location.href)` and `URLSearchParams.set` rather than string concatenation.

## Pairs with

- [ImpersonateBanner](impersonateBanner.md) — issue #16, the top banner that signals an active impersonation session.
