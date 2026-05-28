# EmailLayout Component

## Description

A reusable HTML email layout for Symfony Mailer's `TemplatedEmail`. Replaces the abandoned `@EnabelLayout/emails/base.html.twig` from `enabel/layout-bundle`.

The layout is table-based with inline styles (the only thing every email client reliably renders), centered at 600px, with the Enabel-style address + no-reply notice + copyright footer. The body of the email is provided via the `{% block content %}` block.

## Pre-requisite — Twig namespace for inline images

The component renders the logo via Symfony Mailer's `email.image()` helper, which embeds the image inline (CID). The default logo path is `@images/enabel-logo-email.png`. Register the matching Twig namespace in your application:

```yaml
# config/packages/twig.yaml
twig:
    paths:
        '%kernel.project_dir%/public/images': images
```

> [!IMPORTANT]
> The bundle does **not** ship the logo asset. The consuming application is responsible for placing `enabel-logo-email.png` (or whatever path is passed to `logo`) under the directory mapped to the `@images` namespace — e.g. `public/images/enabel-logo-email.png`. A missing asset will surface as `Unable to find template "@images/..."` at render time.

If you don't want inline embedding (or are not using `TemplatedEmail`), set `logo` to `null` and render the header yourself in the `content` block.

## Parameters

| Parameter         | Type      | Description                                                                          | Default                                          |
|:------------------|:----------|:-------------------------------------------------------------------------------------|:-------------------------------------------------|
| `logo`            | `?string` | Twig-namespaced path passed to `email.image()`. Set to `null` to hide the header     | `'@images/enabel-logo-email.png'`                |
| `address`         | `string`  | Organisation name, bold in the footer                                                | `'Belgian Development Agency'`                   |
| `addressLine`     | `string`  | Street + city line, below the address                                                | `'Rue Haute 147 - 1000 Brussels'`                |
| `noreply`         | `?string` | No-reply notice in the footer. Set to `null` to hide it entirely                     | `'Responses to this e-mail will not be read.'`   |
| `copyrightYear`   | `?int`    | Year in the copyright line. Resolves to current year when `null`                     | `null` (→ `date('Y')`)                           |
| `copyrightHolder` | `string`  | Holder name shown after `©` and in the `<title>` tag                                 | `'Enabel'`                                       |

## Usage

### Minimal email

```twig
{# templates/emails/welcome.html.twig #}
{% component 'Enabel:Ux:EmailLayout' %}
    {% block content %}
        <tr>
            <td style="padding: 24px;">
                <p>Welcome, {{ user.displayName }}.</p>
                <p>Your account has been created.</p>
            </td>
        </tr>
    {% endblock %}
{% endcomponent %}
```

Note: the inner table cells (`<tr><td>`) must live inside `{% block content %}` because the outer wrapper is the email's main `<table>`. This matches the table-based markup expected by Outlook and other strict clients.

### Password reset with localized footer

```twig
{# templates/emails/password_reset.html.twig #}
{% component 'Enabel:Ux:EmailLayout' with {
    noreply: 'app.email.noreply'|trans,
} %}
    {% block content %}
        <tr>
            <td style="padding: 24px;">
                <p>Dear {{ user.displayName }},</p>
                <p>You requested a password reset.</p>
                <p>
                    <a href="{{ url('app_reset_password', { token: resetToken.token }) }}"
                       style="display: inline-block; padding: 12px 24px; background-color: #333; color: #fff; text-decoration: none;">
                        Reset password
                    </a>
                </p>
            </td>
        </tr>
    {% endblock %}
{% endcomponent %}
```

### Custom branding (other Enabel app, other holder)

```twig
{% component 'Enabel:Ux:EmailLayout' with {
    logo: '@images/impala-logo.png',
    copyrightHolder: 'Impala',
} %}
    {% block content %}
        <tr><td style="padding: 24px;">…</td></tr>
    {% endblock %}
{% endcomponent %}
```

### No logo (text-only email)

```twig
{% component 'Enabel:Ux:EmailLayout' with { logo: null } %}
    {% block content %}
        <tr><td style="padding: 24px;">…</td></tr>
    {% endblock %}
{% endcomponent %}
```

## Sending the email

The component assumes the `email` Twig global is available, which is the case when the template is rendered through Symfony Mailer's `TemplatedEmail::htmlTemplate()`:

```php
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

$email = (new TemplatedEmail())
    ->to($user->getEmail())
    ->subject('Welcome')
    ->htmlTemplate('emails/welcome.html.twig')
    ->context(['user' => $user]);

$mailer->send($email);
```

If `logo` is set but the template is rendered outside an email context, Twig will fail with "Variable `email` does not exist." — set `logo: null` to disable the inline-image call.
