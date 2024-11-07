Enabel UX Bundle
================

This bundle provides reusable Twig components and utilities for Symfony applications.

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
