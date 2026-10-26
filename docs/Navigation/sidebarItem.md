## Description

A single link of a [Sidebar](sidebar.md). The item is marked active automatically when the current route name equals `activeRoutePrefix` or starts with it followed by a dot.

## Parameters

| Parameter           | Type      | Description                                                                 | Default |
|:--------------------|:----------|:----------------------------------------------------------------------------|:--------|
| `label`             | `string`  | Text of the link                                                            | `''`    |
| `link`              | `string`  | URL of the link                                                             | `#`     |
| `icon`              | `?string` | Symfony UX Icons name displayed before the label                            | `null`  |
| `activeRoutePrefix` | `?string` | Active when the current `_route` equals it or starts with it plus `.`       | `null`  |
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

- The prefix is matched on whole dot-separated segments: `admin.responses` matches `admin.responses` and `admin.responses.delegate`, but neither `admin.responses_archive` nor `admin.responsesettings`.
- An empty or missing `activeRoutePrefix` never activates the item automatically.
- Use `active: true` to force the state when the route name is not enough.
