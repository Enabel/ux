# ImpersonateBanner Component

## Description

A fixed-top warning banner that signals the current user is impersonating someone else, with a clear "Exit impersonation" link. Pairs with `Enabel:Ux:ImpersonateDropdown` (issue #15) which provides the entry point on the navbar side.

The component renders an `28 px` band pinned to the top of the viewport (`position: fixed; z-index: 1031`), above any Bootstrap `.navbar.fixed-top`. It also ships the small CSS trick that makes the rest of the page lay out correctly when the banner is present:

```css
.impersonate-banner ~ .navbar.fixed-top { top: 28px; }
body:has(.impersonate-banner) { --app-navbar-offset: 108px; }
```

Without that pair, the `min-vh-100` content below the navbar ends up either crammed under the banner or leaves a large empty gap.

## Parameters

| Parameter   | Type     | Description                                                              | Default                 |
|:------------|:---------|:-------------------------------------------------------------------------|:------------------------|
| `message`   | `string` | Banner text — typically translated and parameterised with the user name (**required**) | —                       |
| `exitUrl`   | `string` | Link target for the exit-impersonation control (**required**)            | —                       |
| `exitLabel` | `string` | Label of the exit link                                                   | `'Exit impersonation'`  |
| `icon`      | `string` | Icon identifier passed to `ux_icon()`                                    | `'fa6-solid:user-secret'` |

`message` is rendered with `|raw` so the call site can include simple HTML formatting (e.g. `<strong>` around the impersonated user name). Make sure any user-provided substring is escaped before passing it in.

## Usage

### Wired with Symfony's impersonation security feature

```twig
{# templates/base.html.twig — before the navbar #}
{% if is_granted('IS_IMPERSONATOR') %}
    {{ component('Enabel:Ux:ImpersonateBanner', {
        message: 'nav.impersonate.banner'|trans({'%name%': app.user.displayName}),
        exitUrl: path('app_home', { _switch_user: '_exit' }),
        exitLabel: 'nav.impersonate.exit'|trans,
    }) }}
{% endif %}

<nav class="navbar fixed-top">…</nav>
```

The `{% if is_granted('IS_IMPERSONATOR') %}` gate keeps the banner — and the CSS layout offset — completely out of the page when nobody is impersonating.

### Localized banner with French message

```twig
{{ component('Enabel:Ux:ImpersonateBanner', {
    message: 'Vous incarnez actuellement <strong>%name%</strong>'|trans({'%name%': app.user.displayName|e}),
    exitUrl: path('app_home', { _switch_user: '_exit' }),
    exitLabel: 'Quitter l\\'incarnation',
}) }}
```

Note the explicit `|e` filter on the substituted user name — the component renders `message` with `|raw` to allow the `<strong>` wrapper.

### Custom icon

```twig
{{ component('Enabel:Ux:ImpersonateBanner', {
    message: 'Impersonating ' ~ app.user.displayName,
    exitUrl: path('app_home', { _switch_user: '_exit' }),
    icon: 'fa6-solid:mask',
}) }}
```

Any [Iconify](https://icones.js.org/) ID works (the same syntax as the other Enabel UX components: `Enabel:Ux:Callout`, `Enabel:Ux:Widget`, …).

## CSS variable convention

The `--app-navbar-offset` variable is set on `<body>` via `body:has(.impersonate-banner)`. If your project layout reads that variable to set `padding-top` (e.g. `body { padding-top: var(--app-navbar-offset, 80px); }`), the banner-on-top-of-a-fixed-navbar case will lay out correctly out of the box.

If you don't use that variable, the banner still renders fine — but you may want to either bump your fixed body padding by 28 px when the banner is present, or adopt the variable in your layout.

## Pairs with

- [ImpersonateDropdown](impersonateDropdown.md) — issue #15, the navbar-side widget that lets ROLE_ALLOWED_TO_SWITCH users enter impersonation mode.
