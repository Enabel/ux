## Description

A vertical Bootstrap `nav` for side menus, typically used in the `sidebar` block of the [AdminLayout](../Layout/adminLayout.md). Items are [SidebarItem](sidebarItem.md) components.

## Parameters

| Parameter | Type      | Description                                           | Default |
|:----------|:----------|:------------------------------------------------------|:--------|
| `label`   | `?string` | Accessible name of the navigation (`aria-label`)      | `null`  |

## Blocks

| Block     | Description                         |
|:----------|:------------------------------------|
| `content` | The `SidebarItem` list items        |

## Usage

```twig
<twig:Enabel:Ux:Sidebar label="Administration">
    <twig:Enabel:Ux:SidebarItem label="Security events" link="{{ path('admin.security_events') }}" activeRoutePrefix="admin.security_events" icon="bi:shield-lock" />
    <twig:Enabel:Ux:SidebarItem label="Responses" link="{{ path('admin.responses') }}" activeRoutePrefix="admin.responses" />
</twig:Enabel:Ux:Sidebar>
```
