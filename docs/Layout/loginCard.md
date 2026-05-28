# LoginCard Component

## Description

A full-screen layout for login, password reset, and similar small-form pages: a coloured (or image) background filling the viewport, with a centered Bootstrap card holding an optional logo, an optional title, and a `{% block content %}` for the form.

Mutualises the markup recurring across Enabel apps (login, forgot password, reset password, external-login confirmation, …) where each project copied the same `card shadow-lg` + `border-radius: 1rem` + centered logo + h3 title structure.

## Parameters

| Parameter         | Type      | Description                                                                                          | Default     |
|:------------------|:----------|:-----------------------------------------------------------------------------------------------------|:------------|
| `logo`            | `?string` | Path passed to `asset()` for the top logo. Hidden when `null`                                        | `null`      |
| `title`           | `?string` | Heading rendered inside the card (`h1.h3`). Hidden when `null`                                       | `null`      |
| `background`      | `string`  | Bootstrap theme colour: `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark` | `'primary'` |
| `backgroundImage` | `?string` | Path passed to `asset()` for a background image. When set, overrides `background` (set to `cover`/`center`) | `null`      |
| `size`            | `string`  | Card width: `sm` (360 px), `md` (480 px), `lg` (640 px), `xl` (800 px)                               | `'md'`      |

The component also forwards `class` and other HTML attributes onto the outer wrapper via the standard Twig Component attribute API.

## Usage

### Basic login page

```twig
{# templates/auth/login.html.twig #}
{% extends 'base.html.twig' %}

{% block header %}{% endblock %}
{% block footer %}{% endblock %}

{% block body %}
    {% component 'Enabel:Ux:LoginCard' with {
        logo: asset('images/enabel-logo.png'),
        title: 'app.name'|trans,
        background: 'primary',
    } %}
        {% block content %}
            {{ form_start(form) }}
                {{ form_row(form.username) }}
                {{ form_row(form.password) }}
                <button type="submit" class="btn btn-primary w-100">{{ 'auth.login'|trans }}</button>
            {{ form_end(form) }}
        {% endblock %}
    {% endcomponent %}
{% endblock %}
```

### Password reset (no logo, narrow size)

```twig
{% component 'Enabel:Ux:LoginCard' with {
    title: 'auth.reset.title'|trans,
    size: 'sm',
    background: 'light',
} %}
    {% block content %}
        {{ form(form) }}
    {% endblock %}
{% endcomponent %}
```

### Photo background

```twig
{% component 'Enabel:Ux:LoginCard' with {
    logo: 'images/enabel-logo.png',
    title: 'app.name'|trans,
    backgroundImage: 'images/login-background.jpg',
} %}
    {% block content %}{# form here #}{% endblock %}
{% endcomponent %}
```

When `backgroundImage` is set, the `background` colour is ignored — the image is rendered with `background-size: cover` and `background-position: center`.

## Rendering inside a modal

The component is designed for a full-viewport login page (`min-vh-100`). If you need the same card inside a Bootstrap modal (typical for an "external login" confirmation dialog), the cleanest path is to override the template:

1. Copy `vendor/enabel/ux/templates/layout/login_card.html.twig` to `templates/bundles/EnabelUx/layout/login_card.html.twig`
2. Remove the outer `enabel-login-card` wrapper (`min-vh-100`, `bg-*`, etc.) and keep only the `<div class="card">…</div>` body
3. Wrap with `<div class="modal-dialog modal-lg modal-dialog-centered">` at the call site

See the bundle override mechanism in the main [documentation](../index.md#how-to-override-templates).

## Notes

- The card uses `shadow-lg` (Bootstrap utility) and an inline `border-radius: 1rem` — change via template override if your design system uses a different radius.
- Inner padding is `p-4 p-md-5` (Bootstrap utilities), matches the spacing used in the previous copy-pasted markup across Enabel apps.
