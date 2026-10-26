## Description

A vertical Bootstrap `nav` for side menus, typically used in the `sidebar` block of the [AdminLayout](../Layout/adminLayout.md). Items are [SidebarItem](sidebarItem.md) components, optionally grouped by [SidebarSection](sidebarSection.md) headings. The sidebar has a dark background following the Enabel theme (see the stylesheet in the [AdminLayout](../Layout/adminLayout.md) docs).

## Markup and styling

The sidebar no longer uses the Bootstrap `nav` / `nav-link` / `nav-item` classes (changed in 2.8.0). Its look comes from `enabel-ux-admin.css`, linked by the component through AssetMapper (required), and from its `--enabel-ux-sidebar-*` custom properties (`-bg`, `-accent`, `-color`), see the [AdminLayout](../Layout/adminLayout.md#stylesheet) docs.

## Parameters

| Parameter | Type      | Description                                           | Default |
|:----------|:----------|:------------------------------------------------------|:--------|
| `label`   | `?string` | Accessible name of the navigation (`aria-label`)      | `null`  |

## Blocks

| Block     | Description                         |
|:----------|:------------------------------------|
| `content` | The `SidebarSection` and `SidebarItem` list items |

## Usage

```twig
<twig:Enabel:Ux:Sidebar label="Administration">
    <twig:Enabel:Ux:SidebarSection label="Security" />
    <twig:Enabel:Ux:SidebarItem label="Security events" link="{{ path('admin.security_events') }}" activeRoutePrefix="admin.security_events" icon="bi:shield-lock" />
    <twig:Enabel:Ux:SidebarItem label="Responses" link="{{ path('admin.responses') }}" activeRoutePrefix="admin.responses" />

    <twig:Enabel:Ux:SidebarItem class="mt-auto" label="Back to the app" link="{{ path('home') }}" icon="bi:box-arrow-left" />
</twig:Enabel:Ux:Sidebar>
```

The sidebar is a flex column filling the height of its column: an item with `class="mt-auto"` is pushed to the bottom.
