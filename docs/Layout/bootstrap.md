# Bootstrap Layout

This document describes the Bootstrap layout and how to use it in your Twig templates.

## Overview

The layout is defined in:
- `templates/layout/bootstrap/base.html.twig` (main layout)
- `templates/layout/bootstrap/_header.html.twig` (header with navbar)
- `templates/layout/bootstrap/_footer.html.twig` (footer)

It provides:
- An HTML5 skeleton with a complete `<head>` (favicon, meta, CSS/JS via CDN)
- A configurable navbar (logo, app name, menus, locale switcher)
- A flash messages area rendered as alerts
- Clear Twig blocks to customize each part

## Requirements

This layout require `symfony/asset-mapper` to work.

## Dependencies

By default, the layout loads:
- Bootstrap 5.3 (JS via CDN)
- Enabel Bootstrap Theme 3.x (CSS via CDN)

You can override the `stylesheets` or `javascripts` blocks to use your own assets.

## Translations and parameters

The layout relies on translation keys for application information and menu labels.

Expected keys (files `translations/messages.*.yaml`):

```yaml
params:
  application:
    name: 'EnabelApp'
    description: 'Another Enabel App build with Symfony'
    copyright: 'Enabel'

menu:
  navbar:
    home: 'Home'
    login: 'Login'
    logout: 'Logout'
    profile: 'Profile'
    admin: 'Admin'
```

A French translation file is available at `translations/messages.fr.yaml`.
An English translation file is available at `translations/messages.en.yaml`.

## Basic usage

To use the layout, extend `@EnabelUx/layout/bootstrap/base.html.twig` and fill in your blocks.

```twig
{# templates/page/example.html.twig #}
{% extends '@EnabelUx/layout/bootstrap/base.html.twig' %}

{% block title %}{{ 'params.application.name'|trans }} | Example Page{% endblock %}

{% block body %}
    <h1>My page content</h1>
    <p>Welcome to your page.</p>
{% endblock %}
```

## Block structure

The layout exposes the following blocks:
- `meta_tags`: metadata and `<meta>` tags
- `title`: the `<title>` content
- `favicon`: the full favicon set
- `stylesheets`: additional CSS
- `javascripts`: additional scripts
- `header`: includes the provided navbar by default
- `main`: `<main>` wrapper + container
  - `flash`: renders flash messages using `Enabel:Ux:Alert`
  - `body`: the main page content
- `footer`: includes the provided footer by default

You can override any block in your templates.

## Navbar and menus

The navbar is rendered using the `Enabel:Ux:Navbar` component in `_header.html.twig`.

Key points:
- Logo: `public/bundles/enabelux/images/enabel-logo.png` (path resolved via `asset(...)`)
- Application name: `{{ 'params.application.name'|trans }}`
- Two menus:
  - Left-aligned menu with the “Home” entry (key `menu.navbar.home`)
  - Right-aligned menu with:
    - `LocaleSwitcher` (language selector)
    - Either a user menu (profile, admin, logout) when `app.user` is present
    - Or a “Login” link when not authenticated

Simplified excerpt:

```twig
{% component 'Enabel:Ux:Navbar' with {
    logo: asset('bundles/enabelux/images/enabel-logo.png'),
    name: 'params.application.name'|trans,
    link: '/',
    theme: 'dark',
    expand: 'lg',
    fixed: true,
    fixedPosition: 'top'
} %}
    {% block content %}
        {# Left menu #}
        {% component 'Enabel:Ux:Menu' with { align: 'start' } %}
            {% block content %}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'menu.navbar.home'|trans,
                    link: '/',
                    icon: 'ph:house-fill'
                }) }}
            {% endblock %}
        {% endcomponent %}

        {# Right menu #}
        {% component 'Enabel:Ux:Menu' with { align: 'end' } %}
            {% block content %}
                {{ component('Enabel:Ux:LocaleSwitcher') }}
                {# … items depending on app.user … #}
            {% endblock %}
        {% endcomponent %}
    {% endblock %}
{% endcomponent %}
```

To add menu items, open `_header.html.twig` and add more `MenuItem` components in the appropriate block.

## Flash messages

In the `flash` block, each session message is rendered using the `Enabel:Ux:Alert` component with the option `dismissible: true`.

```twig
{% for type, messages in app.flashes %}
    {% for message in messages %}
        {{ component('Enabel:Ux:Alert', {
            text: message,
            type: type,
            dismissible: true
        }) }}
    {% endfor %}
{% endfor %}
```

Supported types: typically `success`, `info`, `warning`, `danger` (depending on the Alert component).

## Customization

- Fixed header: `fixed: true`, position: `fixedPosition: 'top'`
- Navbar theme: `theme: 'dark'` (or `light` depending on your theme)
- Expansion breakpoint: `expand: 'lg'` (Bootstrap values: `sm`, `md`, `lg`, `xl`, ...)
- Icons: examples use `ph:*` identifiers (Phosphor Icons). Adapt to your icon library.

For deeper customization, you can:
- Override the `header` block to fully replace the navbar
- Override `stylesheets`/`javascripts` to load your assets
- Modify/extend `_footer.html.twig` for your footer

## Best practices

- Centralize texts in translation files to facilitate internationalization.
- Prefer extending the layout `base.html.twig` rather than copying it to benefit from updates.
- Create your own copy of `_header.html.twig` to customize it via components/menus.

## Minimal complete example

```twig
{% extends '@EnabelUx/layout/bootstrap/base.html.twig' %}

{% block title %}{{ 'params.application.name'|trans }}{% endblock %}

{% block body %}
    <div class="py-5">
        <h1 class="display-6">Hello EnabelUx</h1>
        <p class="text-secondary">My first page with the Bootstrap layout.</p>
    </div>
{% endblock %}
```