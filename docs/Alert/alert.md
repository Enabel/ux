## Description

A Bootstrap alert component for displaying messages with customizable types and dismissible functionality.

## Parameters

| Parameter     | Type     | Description                                                                      | Default |
|:--------------|:---------|:---------------------------------------------------------------------------------|:--------|
| `text`        | `string` | The message text to display (can contain HTML)                                   | `null`  |
| `type`        | `string` | The alert type (primary, secondary, success, danger, warning, info, light, dark) | `info`  |
| `dismissible` | `bool`   | Whether the alert can be dismissed by the user                                   | `false` |

## Usage

### Basic usage

```twig
{{ component('Enabel:Ux:Alert', {
    text: 'This is an info message'
}) }}
```

### With custom type

```twig
{{ component('Enabel:Ux:Alert', {
    text: 'Operation completed successfully!',
    type: 'success'
}) }}
```

### Dismissible alert

```twig
{{ component('Enabel:Ux:Alert', {
    text: 'This message can be closed',
    type: 'warning',
    dismissible: true
}) }}
```

### All available types

```twig
{{ component('Enabel:Ux:Alert', {
    text: 'Primary alert',
    type: 'primary'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Secondary alert',
    type: 'secondary'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Success alert',
    type: 'success'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Danger alert',
    type: 'danger'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Warning alert',
    type: 'warning'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Info alert',
    type: 'info'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Light alert',
    type: 'light'
}) }}

{{ component('Enabel:Ux:Alert', {
    text: 'Dark alert',
    type: 'dark'
}) }}
```

## Example with Symfony flash messages

```twig
{% for type, messages in app.flashes %}
    {% for message in messages %}
        {{ component('Enabel:Ux:Alert', {
            text: message,
            type: type,
            dismissible: true
        }) }}
    {% endfor %}
{% endfor %}
```

## Example with HTML content

```twig
{{ component('Enabel:Ux:Alert', {
    text: '<strong>Important!</strong> Please read this message carefully.',
    type: 'danger',
    dismissible: true
}) }}
```

## Custom block content

You can override the content block to customize the message display:

```twig
{% component 'Enabel:Ux:Alert' with {
    type: 'info',
    dismissible: true
} %}
    {% block content %}
        <h4 class="alert-heading">Well done!</h4>
        <p>You have successfully completed the task.</p>
        <hr>
        <p class="mb-0">Click the button to continue.</p>
    {% endblock %}
{% endcomponent %}
```
