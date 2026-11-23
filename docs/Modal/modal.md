Modal
-----

The modal component and accompanying helper allow to easily render modals from a dedicated controller. Modals
implemented this way will have their own dedicated URI and entry in the browser history and can be directly linked to.

### Step 1: include the component in your Twig layout

```twig
{{ component('Enabel:Ux:Modal') }}
```

### Step 2: modify your controller and template

```php

use Enabel\Ux\Helper\Modal;

class MyController
{
    public function __construct(private Modal $modal)
    {
    }
    
    public function __invoke(): Response
    {
        // you can use the helper to redirect from the PHP controller
        if ($form->isSubmitted() && $form->isValid()) {
            // ...
            
            // call Modal::redirect() to redirect and reload the entire page,
            // or Modal::redirectModal() to redirect inside the modal instead
            return $this->modal->redirect($redirectUri);
        }
    
        return $this->modal->render(
            // the URI of the page that will be displayed behind the modal
            $backgroundUri,
            // path to the Twig template that will be rendered inside the modal
            $templatePath,
            // additional template variables
            $templateVars
        );
    }
}
```

The background page is rendered in a sub-request, which the Symfony firewall does not run for. The helper therefore:

- only accepts a path of the application (`/...`) as `$backgroundUri`: a URL with a scheme or a host is refused with a 400;
- checks the `access_control` rules of the background path before rendering it (when SecurityBundle is installed), and throws an `AccessDeniedException` when the user cannot access it;
- requests the background on the host, scheme and client address of the current request;
- marks the response `no-store, private`, whatever the background page declares.

Prefer a background generated from a route name. If it comes from the request (a `backUrl` parameter, for instance), it can point to any page of the application the user is allowed to see.

The Twig template for the modal should just render the content of the modal, without any layout.

Optionally, you can specify one of the alternative sizes (sm, lg, xl) by adding the corresponding class to the .modal-content element.

```twig
<div class="modal-content modal-xl">
    <div class=modal-body>
        ...
    </div>
</div>
```

### Step 3: mark your \<a\> and \<form\> elements to trigger the modal

```twig
    <a href="{{ path('modal_route') }}" data-target="modal">Open modal</a>
    
    <form method="post" action="{{ path('modal_route') }}" data-target="modal">...</form>
```

A form without an `action` attribute is submitted to the URI the modal was loaded from, whatever the current URL of the
browser is. Pressing the browser's back button while a modal is displayed closes the modal and returns to the
background page.

The JavaScript tests of the modal controller use Node's built-in test runner and have no dependencies:
`node --test "tests/js/*.test.mjs"`.
