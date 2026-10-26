## Description

A labelled group of [SidebarItem](sidebarItem.md) components inside a [Sidebar](sidebar.md). It renders an `li` containing the small uppercase heading and a nested `ul` labelled by it (`aria-labelledby`, with an id unique per instance), so assistive technologies announce the group.

## Parameters

| Parameter | Type     | Description                   | Default |
|:----------|:---------|:------------------------------|:--------|
| `label`   | `string` | Text of the heading. Required |         |

Extra attributes (for example `class`) are applied to the group `li`. A caller `id` is not used: the section computes its own id.

## Blocks

| Block     | Description                    |
|:----------|:-------------------------------|
| `content` | The `SidebarItem` of the group |

## Usage

```twig
<twig:Enabel:Ux:Sidebar label="Administration">
    <twig:Enabel:Ux:SidebarSection label="Requests">
        <twig:Enabel:Ux:SidebarItem label="Users" link="{{ path('admin.users') }}" activeRoutePrefix="admin.users" />
    </twig:Enabel:Ux:SidebarSection>
</twig:Enabel:Ux:Sidebar>
```

The style comes from `enabel-ux-admin.css`, loaded by the [Sidebar](sidebar.md).
