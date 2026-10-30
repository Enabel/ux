# Admin helpers

Small helpers for hand-written admin controllers, used with [AdminLayout](../Layout/adminLayout.md),
[ConfirmAction](../ConfirmAction/confirmAction.md) and [Table](../Table/table.md).

## CsrfGuard

Checks the token posted by `Enabel:Ux:ConfirmAction` (field `_token`). A missing or invalid
token is a 403: a one-click action has no form to show an error in, and a silent redirect
would hide a forged request. Requires `symfony/security-csrf`.

```php
public function __invoke(Request $request, User $user, CsrfGuard $csrfGuard): Response
{
    $csrfGuard->assertValid($request, 'user-restore-'.$user->getId());
    // ...
}
```

## Flash

`Flash::add($request, $type, $message)` adds a flash message for `AdminLayout` to show, from
an invokable controller that has no `AbstractController::addFlash()`.

## Sort and Pager

See [Table](../Table/table.md#sort).
