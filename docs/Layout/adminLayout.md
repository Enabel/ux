## Description

A page layout for in-house admin screens. It renders the page `<main>` landmark itself: a sidebar column, flash messages, an optional breadcrumb, a title with an actions area, and the content.

## Parameters

| Parameter    | Type     | Description                                                                          | Default |
|:-------------|:---------|:-------------------------------------------------------------------------------------|:--------|
| `title`      | `string` | Page title (`h1`). Required                                                          |         |
| `breadcrumb` | `array`  | List of `{label: string, link?: string or null}`; the last item is the current page          | `[]`    |

Each breadcrumb item needs a `label`. A `link` is rendered as an anchor except on the last item, which is always plain text with `aria-current="page"`. An item whose `link` is `null` or empty is rendered as plain text, never as an empty anchor.

## Blocks

| Block     | Description                                                   |
|:----------|:--------------------------------------------------------------|
| `sidebar` | Sidebar column, usually a [Sidebar](../Navigation/sidebar.md) |
| `actions` | Buttons displayed on the right of the title                   |
| `content` | Page content                                                  |

## Flash messages

Flash messages are rendered above the breadcrumb through the [Alert](../Alert/alert.md) component, dismissible. The `error` flash type is shown as `danger`; the Bootstrap types (`primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark`) are kept as is; any other type falls back to `info`.

## Where to use it

The bundle base layout (`@EnabelUx/layout/bootstrap/base.html.twig`) wraps its `body` block in `<main><div class="container">` and renders the flash messages before it. AdminLayout must therefore not be used in `body`: it would be nested in a second `<main>`, squeezed by `.container`, and the flash messages would already have been consumed by the base. Extend the application base layout and override the `main` block instead, so that AdminLayout provides the `<main>` landmark at full width and is the only flash renderer.

## Usage

```twig
{% extends 'base.html.twig' %}

{% block main %}
    <twig:Enabel:Ux:AdminLayout
        title="Security events"
        :breadcrumb="[
            {label: 'Administration', link: path('admin')},
            {label: 'Security events'},
        ]"
    >
        <twig:block name="sidebar">
            <twig:Enabel:Ux:Sidebar label="Administration">
                <twig:Enabel:Ux:SidebarItem label="Security events" link="{{ path('admin.security_events') }}" activeRoutePrefix="admin.security_events" />
            </twig:Enabel:Ux:Sidebar>
        </twig:block>

        <twig:block name="actions">
            <a class="btn btn-outline-primary btn-sm" href="{{ path('admin.security_events.export') }}">Export</a>
        </twig:block>

        <twig:block name="content">
            <p>Page content.</p>
        </twig:block>
    </twig:Enabel:Ux:AdminLayout>
{% endblock %}
```
