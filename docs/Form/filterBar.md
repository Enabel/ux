# FilterBar

The filters of a list, on one line: a GET form, a "Filter" button and a "Reset" link back to
the current route without its query string. A `SearchType` field takes the remaining width,
with a search icon and its label kept for screen readers. Field errors are shown under each
field. Usually rendered in the `filters` block of [Table](../Table/table.md).

Requires `symfony/form`.

```php
$filters = $this->formFactory->createNamed('', EventFilterType::class, $filter, [
    'method' => 'GET',
    'csrf_protection' => false,
]);
$filters->handleRequest($request);
```

```twig
<twig:Enabel:Ux:FilterBar :form="filters" />
```

| Prop | Type | Description |
|---|---|---|
| `form` | `FormView` | The filter form; its fields are rendered in order, the buttons last |

The form is unnamed (`createNamed('')`) so its fields are plain query parameters, which
[TableHeader](../Table/table.md#tableheader) keeps when sorting.

The other way round, the current `sort` and `direction` are carried through a submit as hidden
inputs, unless the form has a field of that name; `page` is not, so a new filter starts on the
first page. Hidden fields are rendered without a column nor a label. The `attr` of the form and
the `label_attr` of the fields are kept, the bar adds its classes to them.
