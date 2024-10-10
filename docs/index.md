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

### Step 2: modify your controller to return a response using the helper

```php

use Enabel\Ux\Helper\Modal;

class MyController
{
    public function __construct(private Modal $modal)
    {
    }
    
    public function __invoke(): Response
    {
        // if your modal contains a form, you can use the helper to correctly redirect after successful submission
        if ($form->isSubmitted() && $form->isValid()) {
            // ...
            
            return $this->modal->redirect($redirectUri);
        }
    
        return $this->modal->render(
            $backgroundUri, // the URI of the page that will be displayed behind the modal
            $templatePath, // your template should render a <div class="modal-content"> element *without* a layout
            $templateVars
        );
    }
}
```

### Step 3: mark your \<a\> and \<form\> elements to trigger the modal

```twig
    <a href="{{ path('modal_route') }}" data-target="modal">Open modal</a>
    
    <form method="post" action="{{ path('modal_route') }}" data-target="modal">...</form>
```
