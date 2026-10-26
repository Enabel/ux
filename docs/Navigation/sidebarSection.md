## Description

A small uppercase heading that groups the items of a [Sidebar](sidebar.md). It renders a `li` with `role="presentation"`.

## Parameters

| Parameter | Type     | Description                | Default |
|:----------|:---------|:---------------------------|:--------|
| `label`   | `string` | Text of the heading. Required |         |

## Usage

```twig
<twig:Enabel:Ux:Sidebar label="Administration">
    <twig:Enabel:Ux:SidebarSection label="Administration" />
    <twig:Enabel:Ux:SidebarItem label="Users" link="{{ path('admin.users') }}" activeRoutePrefix="admin.users" />
</twig:Enabel:Ux:Sidebar>
```

The style comes from `enabel-ux-admin.css`, loaded by the [AdminLayout](../Layout/adminLayout.md).
