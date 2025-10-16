## Description

A Bootstrap callout component for displaying highlighted information with customizable types, icons, and content. The callout component is perfect for drawing attention to important notes, warnings, or additional information.

## Parameters

| Parameter | Type     | Description                                                                          | Default |
|:----------|:---------|:-------------------------------------------------------------------------------------|:--------|
| `title`   | `string` | The title/heading text to display                                                    | `null`  |
| `message` | `string` | The main message text to display (can contain HTML)                                  | `null`  |
| `type`    | `string` | The callout type (primary, secondary, success, danger, warning, info, light, dark)  | `info`  |
| `icon`    | `string` | The icon to display (using Symfony UX Icons notation like 'lucide:info')           | `null`  |
| `size`    | `string` | The size variant ('sm' for small, null for default)                                | `null`  |

## Usage

### Basic usage

```twig
{{ component('Enabel:Ux:Callout', {
    message: 'This is an informational callout'
}) }}
```

### With title and icon

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'info',
    title: 'Note importante',
    message: 'Ceci est un message d'information.',
    icon: 'lucide:info'
}) }}
```

### Success callout with icon

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'success',
    title: 'Operation Completed',
    message: 'Your changes have been saved successfully.',
    icon: 'lucide:check-circle'
}) }}
```

### Warning callout

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'warning',
    title: 'Important Warning',
    message: 'Please review your changes before proceeding.',
    icon: 'lucide:alert-triangle'
}) }}
```

### Danger/Error callout

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'danger',
    title: 'Error',
    message: 'An error occurred while processing your request.',
    icon: 'lucide:alert-circle'
}) }}
```

### Small size variant

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'info',
    message: 'This is a small callout.',
    size: 'sm'
}) }}
```

### Icon only (no title)

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'primary',
    message: 'This callout has an icon but no title.',
    icon: 'lucide:star'
}) }}
```

## All available types

```twig
{{ component('Enabel:Ux:Callout', {
    title: 'Primary Callout',
    message: 'This is a primary callout for general emphasis.',
    type: 'primary',
    icon: 'lucide:star'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Secondary Callout',
    message: 'This is a secondary callout for less important information.',
    type: 'secondary',
    icon: 'lucide:info'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Success Callout',
    message: 'This indicates a successful operation or positive outcome.',
    type: 'success',
    icon: 'lucide:check-circle'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Danger Callout',
    message: 'This indicates an error or dangerous situation.',
    type: 'danger',
    icon: 'lucide:alert-circle'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Warning Callout',
    message: 'This indicates a warning or caution needed.',
    type: 'warning',
    icon: 'lucide:alert-triangle'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Info Callout',
    message: 'This provides additional information.',
    type: 'info',
    icon: 'lucide:info'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Light Callout',
    message: 'This is a light themed callout.',
    type: 'light',
    icon: 'lucide:sun'
}) }}

{{ component('Enabel:Ux:Callout', {
    title: 'Dark Callout',
    message: 'This is a dark themed callout.',
    type: 'dark',
    icon: 'lucide:moon'
}) }}
```

## Popular Icon Examples

### Common icons for different callout types

```twig
{# Info callouts #}
{{ component('Enabel:Ux:Callout', {
    type: 'info',
    title: 'Information',
    message: 'Here is some helpful information.',
    icon: 'lucide:info'
}) }}

{# Success callouts #}
{{ component('Enabel:Ux:Callout', {
    type: 'success',
    title: 'Success',
    message: 'Operation completed successfully.',
    icon: 'lucide:check-circle'
}) }}

{# Warning callouts #}
{{ component('Enabel:Ux:Callout', {
    type: 'warning',
    title: 'Warning',
    message: 'Please be cautious.',
    icon: 'lucide:alert-triangle'
}) }}

{# Error callouts #}
{{ component('Enabel:Ux:Callout', {
    type: 'danger',
    title: 'Error',
    message: 'Something went wrong.',
    icon: 'lucide:alert-circle'
}) }}

{# Tips and notes #}
{{ component('Enabel:Ux:Callout', {
    type: 'primary',
    title: 'Pro Tip',
    message: 'Use keyboard shortcuts for faster navigation.',
    icon: 'lucide:lightbulb'
}) }}
```

## Custom block content

You can override the content block to customize the message display:

```twig
{% component 'Enabel:Ux:Callout' with {
    type: 'success',
    title: 'Congratulations!',
    icon: 'lucide:trophy'
} %}
    {% block content %}
        <p><strong>You have completed all the tasks!</strong></p>
        <p>Here's what you accomplished:</p>
        <ul>
            <li>✅ Set up your profile</li>
            <li>✅ Connected your accounts</li>
            <li>✅ Completed the tutorial</li>
        </ul>
        <p class="mb-0">You're now ready to start using the application.</p>
    {% endblock %}
{% endcomponent %}
```

## Advanced usage with attributes

```twig
{{ component('Enabel:Ux:Callout', {
    type: 'warning',
    title: 'Custom Callout',
    message: 'This callout has additional CSS classes and attributes.',
    icon: 'lucide:settings'
}, {
    class: 'my-custom-class border-2',
    'data-bs-toggle': 'tooltip',
    'data-bs-title': 'This is a tooltip'
}) }}
```

## Responsive design

The callout component is fully responsive and works well on all screen sizes. For mobile-first design, consider using the small size variant when space is limited:

```twig
{# Use small variant on smaller screens #}
<div class="d-block d-md-none">
    {{ component('Enabel:Ux:Callout', {
        type: 'info',
        message: 'Mobile-optimized message',
        size: 'sm'
    }) }}
</div>

{# Use default size on larger screens #}
<div class="d-none d-md-block">
    {{ component('Enabel:Ux:Callout', {
        type: 'info',
        title: 'Desktop Information',
        message: 'More detailed message for larger screens',
        icon: 'lucide:monitor'
    }) }}
</div>
```

## Accessibility

The callout component includes proper semantic markup and works well with screen readers. When using icons, ensure they provide meaningful information that supplements the text content.

## CSS Classes Generated

The component generates the following CSS classes based on the Enabel Bootstrap Theme:

- Base class: `callout`
- Type classes: `callout-primary`, `callout-secondary`, `callout-success`, `callout-danger`, `callout-warning`, `callout-info`, `callout-light`, `callout-dark`
- Size class: `callout-sm` (when `size: 'sm'` is used)