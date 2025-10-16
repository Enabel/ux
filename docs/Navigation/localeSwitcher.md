## Description

A locale switcher dropdown for Bootstrap navbar.

## Parameters

| Parameter          | Type    | Description                                     | Default        |
|:-------------------|:--------|:------------------------------------------------|:---------------|
| `locales`          | `array` | Available locales for the application           | `['fr', 'en']` |
| `show_locale_name` | `bool`  | Whether to show the locale name in the switcher | `true`         |


## Usage

```twig
{{ component('Enabel:Ux:LocaleSwitcher') }}
```

## Example in a Bootstrap navbar
```twig
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Enabel Ux</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                ...
            </ul>
            <div class="d-flex align-items-end me-3">
                {{ component('Enabel:Ux:LocaleSwitcher') }}                
            </div>
        </div>
    </div>
</nav>
```

## Example in a Enabel:Ux:Navbar with Enabel:Ux:Menu & Enabel:Ux:MenuItem components
```twig
{% component 'Enabel:Ux:Navbar' with {
    name: 'Enabel Ux',
    link: '/'
} %}
    {% block content %}
        {# Left-aligned menu with navigation items #}
        {% component 'Enabel:Ux:Menu' with { align: 'start' } %}
            {% block content %}
                {{ component('Enabel:Ux:MenuItem', {
                    label: 'Home',
                    link: '/',
                }) }}
                ...
            {% endblock %}
        {% endcomponent %}

        {# Right-aligned menu with user dropdown #}
        {% component 'Enabel:Ux:Menu' with { align: 'end' } %}
            {% block content %}
                {{ component('Enabel:Ux:LocaleSwitcher', {
                    'locales': ['fr', 'en', 'es']
                }) }}
                Other menu items...
            {% endblock %}
        {% endcomponent %}
    {% endblock %}
{% endcomponent %}
```