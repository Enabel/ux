# Navbar Component

## Description

The main navigation container component for creating responsive Bootstrap navigation bars. The Navbar component serves as the top-level container that holds the brand/logo and contains Menu components.

**Component Nesting:**
- **Navbar** (this component) is the top-level container
  - Contains one or more **Menu** components inside its `content` block
    - Each Menu contains one or more **MenuItem** components

## Parameters

| Parameter       | Type      | Description                                                                 | Default   |
|:----------------|:----------|:----------------------------------------------------------------------------|:----------|
| `logo`          | `string`  | URL to the logo image                                                       | `null`    |
| `name`          | `string`  | The brand/application name                                                  | `null`    |
| `link`          | `string`  | The URL for the brand link                                                  | `/`       |
| `theme`         | `string`  | The navbar theme (light, dark)                                             | `light`   |
| `expand`        | `string`  | Breakpoint for navbar expansion (sm, md, lg, xl, xxl, always, never)      | `lg`      |
| `fixed`         | `bool`    | Whether the navbar is fixed                                                 | `false`   |
| `fixedPosition` | `string`  | Position when fixed (top, bottom)                                          | `top`     |

## Usage

### Basic navbar

```twig
{{ component('Enabel:Ux:Navbar', {
    name: 'My Application'
}) }}
```

### Navbar with logo

```twig
{{ component('Enabel:Ux:Navbar', {
    logo: '/images/logo.png',
    name: 'My Application',
    link: '/home'
}) }}
```

### Dark navbar

```twig
{{ component('Enabel:Ux:Navbar', {
    name: 'My Application',
    theme: 'dark'
}) }}
```

### Fixed navbar

```twig
{{ component('Enabel:Ux:Navbar', {
    name: 'My Application',
    fixed: true,
    fixedPosition: 'top'
}) }}
```

### Navbar with custom expand breakpoint

```twig
{{ component('Enabel:Ux:Navbar', {
    name: 'My Application',
    expand: 'md'
}) }}
```

### Complete navbar with nested Menu and MenuItem components

This example shows how to nest Menu components (which contain MenuItem components) inside the Navbar:

```twig
{% component 'Enabel:Ux:Navbar' with {
    logo: '/images/logo.png',
    name: 'My Application',
    link: '/',
    theme: 'dark',
    expand: 'lg'
} %}
    {% block content %}
        {# Menu component is nested inside Navbar #}
        {% component 'Enabel:Ux:Menu' %}
            {% block content %}
                {# MenuItem components are nested inside Menu #}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Home',
                    link: '/',
                    active: true
                }) }}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'About',
                    link: '/about'
                }) }}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Contact',
                    link: '/contact'
                }) }}
            {% endblock %}
        {% endcomponent %}
    {% endblock %}
{% endcomponent %}
```

## Complete Example

Here's a complete example with multiple menus (left and right aligned) showing the full nesting structure:

```twig
{% component 'Enabel:Ux:Navbar' with {
    logo: '/images/logo.png',
    name: 'Enabel Ux',
    link: '/',
    theme: 'dark',
    expand: 'lg',
    fixed: true,
    fixedPosition: 'top'
} %}
    {% block content %}
        {# Left-aligned menu with navigation items #}
        {% component 'Enabel:Ux:Menu' with { align: 'start' } %}
            {% block content %}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Home',
                    link: '/',
                    icon: 'bi:house'
                }) }}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Rides',
                    link: '/rides',
                    icon: 'bi:bicycle'
                }) }}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Profile',
                    link: '/profile',
                    icon: 'bi:person'
                }) }}
            {% endblock %}
        {% endcomponent %}

        {# Right-aligned menu with user dropdown #}
        {% component 'Enabel:Ux:Menu' with { align: 'end' } %}
            {% block content %}
                {% if app.user %}
                    {{ component('Enabel:Ux:MenuItem', {
                        label: app.user.username,
                        icon: 'bi:person-circle',
                        isDropdown: true,
                        dropdownItems: [
                            {label: 'My Account', link: '/account', icon: 'bi:person'},
                            {label: 'Settings', link: '/settings', icon: 'bi:gear'},
                            {divider: true},
                            {label: 'Logout', link: '/logout', icon: 'bi:box-arrow-right'}
                        ]
                    }) }}
                {% else %}
                    {{ component('Enabel:Ux:MenuItem', {
                        label: 'Login',
                        link: '/login',
                        icon: 'bi:box-arrow-in-right'
                    }) }}
                {% endif %}
            {% endblock %}
        {% endcomponent %}
    {% endblock %}
{% endcomponent %}
```

## Customization

### Custom brand content

You can override the brand block to customize the logo and name display:

```twig
{% component 'Enabel:Ux:Navbar' %}
    {% block brand %}
        <a class="navbar-brand" href="/">
            <img src="/logo.png" alt="Logo" height="40">
            <span class="fw-bold">Custom Brand</span>
        </a>
    {% endblock %}
    
    {% block content %}
        {# Menu components here #}
    {% endblock %}
{% endcomponent %}
```

## See Also

- [Menu Component](menu.md) - Container for organizing navigation items (nested inside Navbar)
- [MenuItem Component](menuItem.md) - Individual navigation links (nested inside Menu)
