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
{{ component('EnabelUx:Alert', {
    text: 'This is an info message'
}) }}
```

### With custom type

```twig
{{ component('EnabelUx:Alert', {
    text: 'Operation completed successfully!',
    type: 'success'
}) }}
```

### Dismissible alert

```twig
{{ component('EnabelUx:Alert', {
    text: 'This message can be closed',
    type: 'warning',
    dismissible: true
}) }}
```

### All available types

```twig
{{ component('EnabelUx:Alert', {
    text: 'Primary alert',
    type: 'primary'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Secondary alert',
    type: 'secondary'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Success alert',
    type: 'success'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Danger alert',
    type: 'danger'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Warning alert',
    type: 'warning'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Info alert',
    type: 'info'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Light alert',
    type: 'light'
}) }}

{{ component('EnabelUx:Alert', {
    text: 'Dark alert',
    type: 'dark'
}) }}
```

## Example with Symfony flash messages

```twig
{% for type, messages in app.flashes %}
    {% for message in messages %}
        {{ component('EnabelUx:Alert', {
            text: message,
            type: type,
            dismissible: true
        }) }}
    {% endfor %}
{% endfor %}
```

## Example with HTML content

```twig
{{ component('EnabelUx:Alert', {
    text: '<strong>Important!</strong> Please read this message carefully.',
    type: 'danger',
    dismissible: true
}) }}
```

## Custom block content

You can override the content block to customize the message display:

```twig
{% component 'EnabelUx:Alert' with {
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
