## Description

A locale switcher dropdown for Bootstrap navbar. Each entry shows the country flag — rounded, with a subtle border, and the locale's own-language name as `title` — optionally followed by a label.

Flags render in a fixed 4:3 box (`1.5rem × 1.125rem`) with `preserveAspectRatio="xMidYMid slice"`, so they fill the box and the rounded corners hug the flag. The `cif:*` icons do not share one aspect ratio (`cif:nl` is 2:1, `cif:gb` is 3:2), and both the width and the height are set explicitly because a host application's `ux_icons.default_icon_attributes` would otherwise force them square.

## Parameters

| Parameter         | Type      | Description                                                                                  | Default        |
|:------------------|:----------|:---------------------------------------------------------------------------------------------|:---------------|
| `locales`         | `array`   | Available locales for the application                                                        | `['en', 'fr']` |
| `labelFormat`     | `string`  | Text next to the flag: `name` (Français), `code` (FR) or `none` (flag only)                   | `'name'`       |
| `header`          | `?string` | Heading rendered at the top of the dropdown, e.g. `'Change language'`. Translate at the call site | `null`         |
| `showLocaleName`  | `bool`    | **Deprecated** — use `labelFormat`. `false` still resolves `labelFormat` to `none`            | `true`         |

`labelFormat` defaults to `name` unless `showLocaleName: false` was passed, in which case it resolves to `none`. An explicit `labelFormat` always wins over `showLocaleName`.

## Usage

```twig
{{ component('Enabel:Ux:LocaleSwitcher') }}
```

### Compact switcher with a dropdown heading

Flag plus uppercase locale code — keeps the navbar tight when the app runs three or more locales:

```twig
{{ component('Enabel:Ux:LocaleSwitcher', {
    locales: enabled_locales,
    labelFormat: 'code',
    header: 'navigation.language.change'|trans,
}) }}
```

Renders `🇬🇧 EN` in the navbar, and a dropdown headed *Change language* listing `🇫🇷 FR` and `🇳🇱 NL`.

### Flag only

```twig
{{ component('Enabel:Ux:LocaleSwitcher', { labelFormat: 'none' }) }}
```

## Example in a Bootstrap navbar
```twig
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Enabel Ux</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                ...
            </ul>
            <div class="d-flex align-items-end me-3">
                {{ component('Enabel:Ux:LocaleSwitcher') }}                
            </div>
        </div>
    </div>
</nav>
```

## Example in a Enabel:Ux:Navbar with Enabel:Ux:Menu & Enabel:Ux:MenuItem components
```twig
{% component 'Enabel:Ux:Navbar' with {
    name: 'Enabel Ux',
    link: '/'
} %}
    {% block content %}
        {# Left-aligned menu with navigation items #}
        {% component 'Enabel:Ux:Menu' with { align: 'start' } %}
            {% block content %}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Home',
                    link: '/',
                }) }}
                ...
            {% endblock %}
        {% endcomponent %}

        {# Right-aligned menu with user dropdown #}
        {% component 'Enabel:Ux:Menu' with { align: 'end' } %}
            {% block content %}
                {{ component('Enabel:Ux:LocaleSwitcher', {
                    'locales': ['fr', 'en', 'es']
                }) }}
                Other menu items...
            {% endblock %}
        {% endcomponent %}
    {% endblock %}
{% endcomponent %}
```