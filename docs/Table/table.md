# Table

A paginated list in a card: optional filters, the table, the range ("1–25 of 30") and the
pagination. The calling template renders the header cells and the cells of each row.

Requires `babdev/pagerfanta-bundle` and `pagerfanta/twig` (the bundle does not install it, and the
`pagerfanta()` Twig function comes from it), plus `pagerfanta/doctrine-orm-adapter` for a Doctrine query:

```bash
composer require babdev/pagerfanta-bundle pagerfanta/twig pagerfanta/doctrine-orm-adapter
```

## Usage

```php
use Enabel\Ux\Admin\Pager;
use Enabel\Ux\Admin\Sort;
use Pagerfanta\Doctrine\ORM\QueryAdapter;

private const array SORTABLE = ['date' => 'e.occurredAt', 'actor' => 'e.actor'];

public function __invoke(Request $request): Response
{
    $sort = Sort::fromRequest($request, self::SORTABLE, 'date');
    $qb = $this->repository->createQueryBuilder('e')
        ->orderBy($sort->path(self::SORTABLE), $sort->direction);

    return $this->render('admin/event/index.html.twig', [
        'pager' => Pager::fromAdapter(new QueryAdapter($qb), $request, 50),
        'sort' => $sort,
        'filters' => $filters->createView(),
    ]);
}
```

```twig
<twig:Enabel:Ux:Table :pager="pager" emptyMessage="{{ 'admin.event.empty' | trans }}">
    <twig:block name="filters">
        <twig:Enabel:Ux:FilterBar :form="filters" />
    </twig:block>
    <twig:block name="head">
        <twig:Enabel:Ux:TableHeader label="{{ 'admin.event.date' | trans }}" field="date" :sort="sort" />
        <twig:Enabel:Ux:TableHeader label="{{ 'admin.event.actor' | trans }}" field="actor" :sort="sort" />
        <twig:Enabel:Ux:TableHeader label="{{ 'admin.event.details' | trans }}" />
    </twig:block>
    <twig:block name="row">
        <td>{{ item.occurredAt | format_datetime('short', 'short') }}</td>
        <td>{{ item.actor }}</td>
        <td>{{ item.details }}</td>
    </twig:block>
</twig:Enabel:Ux:Table>
```

## Table

| Prop | Type | Description |
|---|---|---|
| `pager` | `PagerfantaInterface` | The rows of the current page, as `item` in the `row` block |
| `emptyMessage` | `string` | Shown when there is no row |

Blocks: `filters` (optional, shown in the card header), `head` (the header cells), `row` (the
cells of `item`). Extra attributes go on the card.

## TableHeader

| Prop | Type | Description |
|---|---|---|
| `label` | `string` | The column title |
| `field` | `?string` | The key of the column in the sort allow-list; none for a column that does not sort |
| `sort` | `?Sort` | The current sort |

A sortable header links to the current route sorted on its field, in the opposite direction
when it is the sorted one (descending first otherwise), keeping the other query parameters and
going back to the first page. It carries `aria-sort` and an arrow on the sorted column. Without
a route (e.g. rendered outside a request), it is a plain header.

## Sort

`Sort::fromRequest($request, $allowed, $defaultField, $defaultDirection = 'DESC')` reads the
`sort` and `direction` query parameters. The request value is only used as a key of
`$allowed`: `path()` returns the path the controller chose, never the request value. An unknown
field or direction falls back to the default, never to an error.

## Pager

`Pager::fromAdapter($adapter, $request, $maxPerPage)` reads the `page` query parameter. A
garbage page number means the first page; a page past the last one is a 404.
