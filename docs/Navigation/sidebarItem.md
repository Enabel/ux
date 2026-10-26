## Description

A single link of a [Sidebar](sidebar.md). The item is marked active automatically when the current route name starts with `activeRoutePrefix`.

## Parameters

| Parameter           | Type      | Description                                                                 | Default |
|:--------------------|:----------|:----------------------------------------------------------------------------|:--------|
| `label`             | `string`  | Text of the link                                                            | `''`    |
| `link`              | `string`  | URL of the link                                                             | `#`     |
| `icon`              | `?string` | Symfony UX Icons name displayed before the label                            | `null`  |
| `activeRoutePrefix` | `?string` | The item is active when the current `_route` starts with this prefix        | `null`  |
| `active`            | `bool`    | Forces the active state; when given, `activeRoutePrefix` is not evaluated   | `false` |

## Usage

```twig
<twig:Enabel:Ux:SidebarItem
    label="Permissions"
    link="{{ path('admin.permissions') }}"
    activeRoutePrefix="admin.permissions"
    icon="bi:key"
/>
```

With `activeRoutePrefix="admin.permissions"`, the routes `admin.permissions` and `admin.permissions.edit` both activate the item (`active` class and `aria-current="page"`).

## Notes

- An empty or missing `activeRoutePrefix` never activates the item automatically.
- Use `active: true` to force the state when the route name is not enough.
