## Description

A button that opens a Bootstrap confirmation modal. Confirming submits a plain `POST` form (no JavaScript beyond Bootstrap's modal) that carries a CSRF token in the `_token` field. Use it for every write action that must not happen on a simple `GET`.

> **Warning: never place this component inside another `<form>`.** It renders its own `<form>` (the modal). HTML forbids nested forms: the browser drops the inner `<form>` tag, so the confirm button would submit the outer form instead of POSTing to `url`. Place it outside any form, for example in the `actions` block of [AdminLayout](../Layout/adminLayout.md) or in a table row that is not wrapped in a form.

## Requirements

The template calls `csrf_token()`, which needs `symfony/security-csrf` (listed under `suggest` in `composer.json`) and the Twig CSRF extension (provided by `symfony/twig-bridge` once the security-csrf component is installed).

## Parameters

| Parameter      | Type      | Description                                                       | Default          |
|:---------------|:----------|:------------------------------------------------------------------|:-----------------|
| `url`          | `string`  | URL the form is posted to. Required                               |                  |
| `tokenId`      | `string`  | CSRF token id passed to `csrf_token()`. Required                  |                  |
| `label`        | `string`  | Button label. Required                                            |                  |
| `message`      | `string`  | Text displayed in the modal body. Required                        |                  |
| `title`        | `?string` | Modal title                                                       | the `label`      |
| `confirmLabel` | `?string` | Label of the submit button of the modal                           | the `label`      |
| `variant`      | `string`  | `danger`, `primary`, `success`, `warning` or `secondary`          | `danger`         |
| `icon`         | `?string` | Symfony UX Icons name displayed in the button                     | `null`           |
| `size`         | `string`  | `sm` or `md`                                                      | `sm`             |

The modal gets a unique id per instance, so the component can be used several times on the same page (for example once per table row). The cancel button and the close button are translated with the `enabel_ux.confirm_action.cancel` key.

## Usage

```twig
<twig:Enabel:Ux:ConfirmAction
    url="{{ path('admin.users.delete', {id: user.id}) }}"
    tokenId="delete-user-{{ user.id }}"
    label="Delete"
    message="This user will be deleted. Continue?"
    icon="bi:trash"
/>
```

## Controller side

The token must be verified in the controller, and the action must only accept `POST`:

```php
#[Route('/admin/users/{id}/delete', name: 'admin.users.delete', methods: ['POST'])]
public function delete(User $user, Request $request): Response
{
    if (!$this->isCsrfTokenValid('delete-user-'.$user->getId(), $request->request->getString('_token'))) {
        throw $this->createAccessDeniedException();
    }

    // ... perform the action

    return $this->redirectToRoute('admin.users');
}
```

The `tokenId` given to the component and the one given to `isCsrfTokenValid()` must be identical.
