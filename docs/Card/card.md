## Description

A Bootstrap card component for displaying content in a flexible and extensible container with multiple options including header, body, footer, and images.

## Parameters

| Parameter       | Type     | Description                                 | Default        |
|:----------------|:---------|:--------------------------------------------|:---------------|
| `title`         | `string` | The card title displayed in the body        | `null`         |
| `subtitle`      | `string` | The card subtitle displayed below the title | `null`         |
| `text`          | `string` | The card text content (can contain HTML)    | `null`         |
| `image`         | `string` | URL of the image to display                 | `null`         |
| `imageAlt`      | `string` | Alternative text for the image              | `''`           |
| `imagePosition` | `string` | Position of the image: `top` or `bottom`    | `top`          |
| `header`        | `string` | Text or HTML content for the card header    | `null`         |
| `footer`        | `string` | Text or HTML content for the card footer    | `null`         |
| `link`          | `string` | URL for the call-to-action button           | `null`         |
| `linkText`      | `string` | Text for the call-to-action button          | `Go somewhere` |

## Usage

### Basic card with title and text

```twig
{{ component('EnabelUx:Card', {
    title: 'Card title',
    text: 'Some quick example text to build on the card title and make up the bulk of the card\'s content.'
}) }}
```

### Card with image

```twig
{{ component('EnabelUx:Card', {
    image: '/images/example.jpg',
    imageAlt: 'Example image',
    title: 'Card with image',
    text: 'This card displays an image at the top.'
}) }}
```

### Card with image at bottom

```twig
{{ component('EnabelUx:Card', {
    title: 'Card title',
    text: 'This card displays an image at the bottom.',
    image: '/images/example.jpg',
    imageAlt: 'Example image',
    imagePosition: 'bottom'
}) }}
```

### Card with subtitle

```twig
{{ component('EnabelUx:Card', {
    title: 'Card title',
    subtitle: 'Card subtitle',
    text: 'This card includes a subtitle below the title.'
}) }}
```

### Card with header and footer

```twig
{{ component('EnabelUx:Card', {
    header: 'Featured',
    title: 'Special title treatment',
    text: 'With supporting text below as a natural lead-in to additional content.',
    footer: 'Last updated 3 mins ago'
}) }}
```

### Card with link button

```twig
{{ component('EnabelUx:Card', {
    title: 'Card title',
    text: 'Some quick example text to build on the card title.',
    link: '/more-info',
    linkText: 'Learn more'
}) }}
```

### Complete card example

```twig
{{ component('EnabelUx:Card', {
    image: '/images/product.jpg',
    imageAlt: 'Product image',
    imagePosition: 'top',
    header: 'New Product',
    title: 'Amazing Product',
    subtitle: 'Premium Quality',
    text: 'This is an amazing product with exceptional features and benefits.',
    link: '/products/amazing-product',
    linkText: 'View Details',
    footer: 'Available now'
}) }}
```

### Card with custom CSS classes

```twig
{{ component('EnabelUx:Card', {
    title: 'Custom styled card',
    text: 'This card has custom styling.',
    class: 'border-primary shadow-lg'
}) }}
```

## Custom block content

You can override blocks to fully customize the card content:

```twig
{% component 'EnabelUx:Card' %}
    {% block body %}
        <div class="card-body">
            <h5 class="card-title">Custom Card</h5>
            <p class="card-text">This is a completely custom card body.</p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Item 1</li>
                <li class="list-group-item">Item 2</li>
                <li class="list-group-item">Item 3</li>
            </ul>
        </div>
    {% endblock %}
{% endcomponent %}
```

### Customizing the header

```twig
{% component 'EnabelUx:Card' with {
    title: 'Card with custom header',
    text: 'This card has a custom header.'
} %}
    {% block header %}
        <strong>Custom Header</strong> with <em>formatting</em>
    {% endblock %}
{% endcomponent %}
```

### Customizing the footer

```twig
{% component 'EnabelUx:Card' with {
    title: 'Card with custom footer',
    text: 'This card has a custom footer.'
} %}
    {% block footer %}
        <small class="text-muted">Last updated {{ 'now'|date('Y-m-d') }}</small>
    {% endblock %}
{% endcomponent %}
```

### Customizing the card body

```twig
{% component 'EnabelUx:Card' with {
    image: '/images/avatar.jpg',
    imageAlt: 'Avatar'
} %}
    {% block card_body %}
        <h5 class="card-title">John Doe</h5>
        <h6 class="card-subtitle mb-2 text-muted">Software Developer</h6>
        <p class="card-text">Passionate about creating amazing web applications.</p>
        <a href="/profile" class="card-link">View Profile</a>
        <a href="/contact" class="card-link">Contact</a>
    {% endblock %}
{% endcomponent %}
```

## Bootstrap Card Variations

The component supports Bootstrap card utilities through custom CSS classes:

### Text alignment

```twig
{{ component('EnabelUx:Card', {
    title: 'Centered card',
    text: 'This card has centered text.',
    class: 'text-center'
}) }}
```

### Background colors

```twig
{{ component('EnabelUx:Card', {
    title: 'Primary card',
    text: 'This card has a primary background.',
    class: 'text-white bg-primary'
}) }}

{{ component('EnabelUx:Card', {
    title: 'Success card',
    text: 'This card has a success background.',
    class: 'text-white bg-success'
}) }}
```

### Border colors

```twig
{{ component('EnabelUx:Card', {
    title: 'Border card',
    text: 'This card has a colored border.',
    class: 'border-primary'
}) }}
```

### Card sizing

```twig
<div style="width: 18rem;">
    {{ component('EnabelUx:Card', {
        image: '/images/example.jpg',
        imageAlt: 'Example',
        title: 'Card width',
        text: 'This card is constrained to 18rem width.'
    }) }}
</div>
```
