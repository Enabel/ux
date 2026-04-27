# Toast Component

## Description

A Bootstrap 5 toast component for non-intrusive notifications (flash messages, status updates). Renders a `.toast` element styled with `text-bg-{type}`, with optional dismiss button, auto-hide and configurable politeness for screen readers.

The component renders the markup only — Bootstrap's toast must be initialized client-side (via `bootstrap.Toast.getOrCreateInstance(el).show()` or any equivalent), exactly like the native Bootstrap toast.

## Parameters

| Parameter     | Type      | Description                                                                                  | Default   |
|:--------------|:----------|:---------------------------------------------------------------------------------------------|:----------|
| `text`        | `?string` | The message text rendered in the toast body                                                  | `null`    |
| `type`        | `string`  | Bootstrap color: `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark` | `'info'`  |
| `dismissible` | `bool`    | Show a close button                                                                          | `true`    |
| `autohide`    | `bool`    | Map to Bootstrap `data-bs-autohide`                                                          | `true`    |
| `delay`       | `int`     | Auto-hide delay in milliseconds (Bootstrap `data-bs-delay`)                                  | `5000`    |
| `politeness`  | `string`  | `polite` or `assertive`. Maps to `aria-live`                                                 | `'polite'`|

## Accessibility notes

The component renders `role="alert"` with `aria-live="{politeness}"` and `aria-atomic="true"`.

- Use `politeness: 'polite'` (default) for non-urgent updates so the screen reader announces them at the next pause (success, info confirmations).
- Use `politeness: 'assertive'` for urgent feedback that must interrupt (errors, warnings the user must act on).

The close button gets `btn-close-white` automatically except on `warning` and `light` backgrounds (dark text on light fill).

## Usage

### Basic toast

```twig
{{ component('Enabel:Ux:Toast', {
    text: 'Saved successfully',
    type: 'success',
}) }}
```

### Toast with custom delay

```twig
{{ component('Enabel:Ux:Toast', {
    text: 'This will hide in 10 seconds',
    type: 'info',
    delay: 10000,
}) }}
```

### Persistent toast (no auto-hide)

```twig
{{ component('Enabel:Ux:Toast', {
    text: 'Read me carefully and dismiss when done',
    type: 'warning',
    autohide: false,
}) }}
```

### Assertive toast for errors

```twig
{{ component('Enabel:Ux:Toast', {
    text: 'Failed to save your changes',
    type: 'danger',
    politeness: 'assertive',
}) }}
```

### Toast with rich content via the `content` block

```twig
{% component 'Enabel:Ux:Toast' with { type: 'success' } %}
    {% block content %}
        <strong>Saved.</strong> View it <a href="/items/42" class="link-light">here</a>.
    {% endblock %}
{% endcomponent %}
```

## Showing the toast (client-side)

Like the native Bootstrap toast, the component renders the markup but does not display it. Initialize and show via Bootstrap's JS API. Example with a Stimulus controller covering all toasts inside a container:

```js
import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

export default class extends Controller {
    connect() {
        this.element.querySelectorAll('.toast').forEach(el => {
            Toast.getOrCreateInstance(el).show();
        });
    }
}
```

```twig
<div class="toast-container position-fixed top-0 end-0 p-3" data-controller="flash">
    {% for type, messages in app.flashes %}
        {% for message in messages %}
            {{ component('Enabel:Ux:Toast', { text: message, type: type }) }}
        {% endfor %}
    {% endfor %}
</div>
```

Bootstrap automatically stacks multiple toasts inside a `.toast-container` (margin between them) and handles fade-in/fade-out animations.

## Flash type mapping (Symfony)

Symfony's `addFlash()` accepts free-form types. To map them to Bootstrap colors before passing to the component:

```twig
{% set bsType = {success: 'success', error: 'danger', warning: 'warning', info: 'info', notice: 'info'}[type]|default('primary') %}
{{ component('Enabel:Ux:Toast', { text: message, type: bsType }) }}
```
