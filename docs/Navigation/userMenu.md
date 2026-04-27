# UserMenu Component

## Description

A ready-to-use Bootstrap user menu for the navbar. Renders a circular avatar (photo or auto-derived colored initials) that opens a right-aligned dropdown with the user name as header and a configurable list of items (links, dividers, headers).

This component is intended to be nested inside a [Menu](menu.md) component aligned to the end of the [Navbar](navbar.md).

**Component Nesting:**
- **Navbar** is the top-level container
  - Contains one or more **Menu** components inside its `content` block
    - A **Menu** can contain a **UserMenu** (this component) alongside [MenuItem](menuItem.md) components

## Parameters

| Parameter       | Type                | Description                                                                                       | Default                |
|:----------------|:--------------------|:--------------------------------------------------------------------------------------------------|:-----------------------|
| `name`          | `string` (required) | The user display name. Used for the dropdown header, the toggle `aria-label`, and to derive default initials/color | —                      |
| `image`         | `?string`           | URL or data-URI of the user picture. When set, the avatar renders the image instead of initials   | `null`                 |
| `initials`      | `?string`           | Override of the auto-derived initials                                                             | derived from `name`    |
| `initialsColor` | `?string`           | Override of the auto-derived background color (CSS color). Used only when no `image` is provided  | derived from `name`    |
| `dropdownAlign` | `string`            | Dropdown alignment, `start` or `end`                                                              | `'end'`                |
| `hideName`      | `bool`              | Hide the user name next to the avatar in the toggle (the name is still in the dropdown header)    | `true`                 |
| `items`         | `array`             | Dropdown items (each with `label`, `link`, `icon`, `divider`, `header`, `active`, `disabled`, `visible`) | `[]`                   |

## Initials derivation

When `initials` is not provided, they are derived from `name`:
- Two or more words → first letter of the first word + first letter of the last word (e.g., `Damien Lagae` → `DL`, `John Ronald Reuel Tolkien` → `JT`)
- One word → first two characters of the word (e.g., `Damien` → `DA`)
- Always uppercase, multibyte-safe (e.g., `éric Dübois` → `ÉD`)
- Empty/whitespace name → empty string

## Color derivation

When `initialsColor` is not provided, a deterministic color is picked from a curated palette using a CRC32 hash of `name`. The same name always gets the same color, which keeps avatars visually stable across pages and sessions.

## Visible flag for items

Each dropdown item supports a `visible: bool` flag. When `visible` is explicitly `false`, the item is filtered out before rendering. This lets you express conditional items (e.g., admin links protected by a permission check) directly in the items array, without surrounding `{% if %}` blocks:

```twig
{{ component('Enabel:Ux:UserMenu', {
    name: app.user.displayName,
    items: [
        {label: 'Profile', link: path('app_profile'), icon: 'bi:person-circle'},
        {label: 'Admin', link: path('admin_dashboard'), icon: 'bi:gear-fill', visible: is_granted('ROLE_ADMIN')},
        {divider: true},
        {label: 'Logout', link: path('app_logout'), icon: 'bi:box-arrow-right'},
    ],
}) }}
```

## Automatic active detection

Same behavior as [MenuItem](menuItem.md): each item with a `link` is automatically marked active when its link matches the current request path (exact match, or prefix match for non-root paths). Manual `active` always overrides auto-detection. Dividers and headers are skipped.

## Usage

### Minimal usage (auto-derived initials and color)

```twig
{% component 'Enabel:Ux:Navbar' with { name: 'My App' } %}
    {% block content %}
        {% component 'Enabel:Ux:Menu' with { align: 'end' } %}
            {% block content %}
                {{ component('Enabel:Ux:UserMenu', {
                    name: app.user.displayName,
                    items: [
                        {label: 'Profile', link: path('app_profile'), icon: 'bi:person-circle'},
                        {label: 'Logout', link: path('app_logout'), icon: 'bi:box-arrow-right'},
                    ],
                }) }}
            {% endblock %}
        {% endcomponent %}
    {% endblock %}
{% endcomponent %}
```

### With a profile picture

```twig
{{ component('Enabel:Ux:UserMenu', {
    name: app.user.displayName,
    image: app.user.profilePictureDataUri,
    items: [
        {label: 'Profile', link: path('app_profile'), icon: 'bi:person-circle'},
        {label: 'Logout', link: path('app_logout'), icon: 'bi:box-arrow-right'},
    ],
}) }}
```

### With pre-computed initials and color

If your `User` entity already exposes initials and a brand color, override the derivation:

```twig
{{ component('Enabel:Ux:UserMenu', {
    name: app.user.displayName,
    initials: app.user.initials,
    initialsColor: app.user.avatarColor,
    items: [...],
}) }}
```

### Conditional items

```twig
{{ component('Enabel:Ux:UserMenu', {
    name: app.user.displayName,
    items: [
        {label: 'Profile', link: path('app_profile'), icon: 'bi:person-circle'},
        {label: 'Configuration', link: path('admin_dashboard'), icon: 'bi:gear-fill', visible: is_granted('ROLE_ADMIN')},
        {divider: true},
        {label: 'Logout', link: path('app_logout'), icon: 'bi:box-arrow-right'},
    ],
}) }}
```

### Show the user name next to the avatar

```twig
{{ component('Enabel:Ux:UserMenu', {
    name: app.user.displayName,
    hideName: false,
    items: [...],
}) }}
```

## See Also

- [Navbar Component](navbar.md) - Main navigation container
- [Menu Component](menu.md) - Container for organizing navigation items
- [MenuItem Component](menuItem.md) - Individual navigation links or dropdowns
- [LocaleSwitcher Component](localeSwitcher.md) - Locale switcher dropdown
