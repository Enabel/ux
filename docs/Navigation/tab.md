## Description

A Bootstrap nav-tabs/nav-pills component for creating tabbed navigation with content panes. Supports multiple variants, alignment options, and styling configurations.

## Parameters

| Parameter       | Type     | Description                                               | Default  |
|:----------------|:---------|:----------------------------------------------------------|:---------|
| `tabs`          | `array`  | Array of tab items with id, label, link, and content keys | `[]`     |
| `activeTab`     | `string` | ID of the active tab                                      | `null`   |
| `activeContent` | `string` | HTML content displayed in the active tab pane             | `null`   |
| `variant`       | `string` | Navigation style: `tabs` or `pills`                       | `tabs`   |
| `fill`          | `bool`   | Whether tabs should fill available width proportionally   | `false`  |
| `justified`     | `bool`   | Whether tabs should fill available width equally          | `false`  |
| `alignment`     | `string` | Horizontal alignment: `start`, `center`, or `end`         | `start`  |

## Tab Item Structure

Each tab in the `tabs` array can have the following keys:

| Key       | Type     | Description                    | Required |
|:----------|:---------|:-------------------------------|:---------|
| `id`      | `string` | Unique identifier for the tab  | Yes      |
| `label`   | `string` | Text displayed in the tab link | Yes      |
| `link`    | `string` | URL for the tab link           | Yes      |

## Usage

### Basic tabs

```twig
 {{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'home',
            label: 'Home',
            link: '/'
        },
        {
            id: 'profile',
            label: 'Profile',
            link: '/profile'
        },
        {
            id: 'contact',
            label: 'Contact',
            link: '/contact'
        }
    ],
    activeTab: 'home',
    activeContent: '<p>Active tab content goes here.</p>'
}) }}
```

### Pills variant

```twig
{{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'home',
            label: 'Home',
            link: '/'
        },
        {
            id: 'profile',
            label: 'Profile',
            link: '/profile'
        },
        {
            id: 'contact',
            label: 'Contact',
            link: '/contact'
        }
    ],
    activeTab: 'home',
    activeContent: '<p>Active tab content goes here.</p>',
    variant: 'pills'
}) }}
```

### Centered tabs

```twig
{{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'home',
            label: 'Home',
            link: '/'
        },
        {
            id: 'profile',
            label: 'Profile',
            link: '/profile'
        },
        {
            id: 'contact',
            label: 'Contact',
            link: '/contact'
        }
    ],
    activeTab: 'home',
    activeContent: '<p>Active tab content goes here.</p>',
    alignment: 'center'
}) }}
```

### Fill tabs

```twig
{{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'longer-tab',
            label: 'Longer tab title',
            link: '/longer-tab'
        },
        {
            id: 'short',
            label: 'Short',
            link: '/short'
        },
        {
            id: 'medium',
            label: 'Medium tab',
            link: '/medium'
        }
    ],
    activeTab: 'longer-tab',
    activeContent: '<p>Active tab content goes here.</p>',
    fill: true
}) }}
```

### Justified tabs

```twig
{{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'longer-tab',
            label: 'Longer tab title',
            link: '/longer-tab'
        },
        {
            id: 'short',
            label: 'Short',
            link: '/short'
        },
        {
            id: 'medium',
            label: 'Medium tab',
            link: '/medium'
        }
    ],
    activeTab: 'longer-tab',
    activeContent: '<p>Active tab content goes here.</p>',
    justified: true
}) }}
```

### Right-aligned pills

```twig
{{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'longer-tab',
            label: 'Longer tab title',
            link: '/longer-tab'
        },
        {
            id: 'short',
            label: 'Short',
            link: '/short'
        },
        {
            id: 'medium',
            label: 'Medium tab',
            link: '/medium'
        }
    ],
    activeTab: 'longer-tab',
    activeContent: '<p>Active tab content goes here.</p>',
    alignment: 'end'
}) }}
```

### Tabs with links only (no activeContent)

```twig
{{ component('Enabel:Ux:Tab', {
    tabs: [
        {
            id: 'page1',
            label: 'Page 1',
            link: '/page1'
        },
        {
            id: 'page2',
            label: 'Page 2',
            link: '/page2'
        },
        {
            id: 'page3',
            label: 'Page 3',
            link: '/page3'
        }
    ],
    activeTab: 'page1'
}) }}
```

## Custom block content

You can override the tabs or content blocks to customize the display:

```twig
{% component 'Enabel:Ux:Tab' with {
    tabs: [
        {id: 'tab1', label: 'Tab 1', link: '/tab1'},
        {id: 'tab2', label: 'Tab 2', link: '/tab2'}
    ],
    activeTab: 'tab1'
} %}
    {% block content %}
        <div class="card">
            <div class="card-body">
                <h4>Custom Tab Content</h4>
                <p>Your custom content here with full control over markup.</p>
            </div>
        </div>
    {% endblock %}
{% endcomponent %}
```

## Notes

- The `fill` option makes tabs proportionally fill available width based on their content
- The `justified` option makes all tabs equal width
- If both `fill` and `justified` are true, `fill` takes precedence
- The `activeTab` parameter should match one of the tab `id` values
- If no `activeTab` is specified, no tab will be active by default
- The `activeContent` supports HTML (use with caution for user-generated content)
- if no `activeContent` is specified, no content will be displayed
