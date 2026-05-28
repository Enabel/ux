# ErrorPage Component

## Description

A full-page error layout for Symfony's TwigBundle exception templates (`error.html.twig`, `error404.html.twig`, `error500.html.twig`, etc.). Replaces the abandoned `@EnabelLayout/error/base.html.twig` from `enabel/layout-bundle`.

The component renders the full `<!DOCTYPE html>` page (it intentionally does **not** extend `base.html.twig` — error pages must keep working even when the asset pipeline is broken) and emits the markup expected by the theme stylesheet shipped at `@enabel/enabel-bootstrap-theme/dist/css/error.min.css`.

## Pre-requisite

The CSS that styles the error page is shipped by the Enabel Bootstrap Theme. Add it to your importmap:

```bash
symfony console importmap:require "@enabel/enabel-bootstrap-theme/dist/css/error.min.css"
```

If you serve the theme through a different path, override the `stylesheet` parameter (see below).

## Parameters

| Parameter    | Type      | Description                                                              | Default                                                                  |
|:-------------|:----------|:-------------------------------------------------------------------------|:-------------------------------------------------------------------------|
| `statusCode` | `int`     | HTTP status code displayed as the big number (**required**)              | —                                                                        |
| `title`      | `string`  | Heading shown below the status code (**required**)                       | —                                                                        |
| `message`    | `string`  | Body paragraph explaining the error (**required**)                       | —                                                                        |
| `details`    | `?string` | Optional small print (trace IDs, technical details)                      | `null`                                                                   |
| `backUrl`    | `?string` | URL of the "back home" button. Button is hidden when `null`              | `null`                                                                   |
| `backLabel`  | `string`  | Label of the "back home" button                                          | `'Back to homepage'`                                                     |
| `symbol`     | `string`  | Path to the watermark image (`.bg img`)                                  | `'/images/enabel-symbol.png'`                                            |
| `logo`       | `string`  | Path to the bottom logo (`.logo img`)                                    | `'/images/enabel-logo-email.png'`                                        |
| `locale`     | `string`  | Value used for `<html lang>`                                             | `'en'`                                                                   |
| `appName`    | `string`  | Application name used in the `<title>` tag and logo `alt`                | `'Enabel'`                                                               |
| `stylesheet` | `string`  | Path passed to `asset()` for the error CSS                               | `'vendor/@enabel/enabel-bootstrap-theme/dist/css/error.min.css'`         |

## Usage

### Minimal 404 page

```twig
{# templates/bundles/TwigBundle/Exception/error404.html.twig #}
{{ component('Enabel:Ux:ErrorPage', {
    statusCode: 404,
    title: 'app.error.404.title'|trans,
    message: 'app.error.404.message'|trans,
}) }}
```

### With back button and localized labels

```twig
{# templates/bundles/TwigBundle/Exception/error404.html.twig #}
{{ component('Enabel:Ux:ErrorPage', {
    statusCode: 404,
    title: 'app.error.404.title'|trans,
    message: 'app.error.404.message'|trans,
    backUrl: path('app_home'),
    backLabel: 'app.error.btn.backhome'|trans,
    locale: app.request.locale,
    appName: 'app.name'|trans,
}) }}
```

### Generic error template with optional debug details

```twig
{# templates/bundles/TwigBundle/Exception/error.html.twig #}
{{ component('Enabel:Ux:ErrorPage', {
    statusCode: status_code,
    title: status_text,
    message: 'app.error.generic.message'|trans,
    details: app.environment == 'dev' ? exception.message : null,
    backUrl: path('app_home'),
}) }}
```

## Why a full-page component?

Symfony renders TwigBundle exception templates in a context where the regular `base.html.twig` cannot be trusted (the failing request might be the one that broke the asset pipeline). For this reason the component does not extend `base.html.twig` and only loads a single stylesheet via `asset()`.

If you want to ship your own CSS for the error page, override the `stylesheet` parameter or override the template entirely with the standard bundle override mechanism (see the main [documentation](../index.md#how-to-override-templates)).
