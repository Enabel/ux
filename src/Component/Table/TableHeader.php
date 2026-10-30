<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Table;

use Enabel\Ux\Admin\Sort;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * A header cell of Enabel:Ux:Table. Given a field and the current Sort, it
 * links to the list sorted on that field, keeping the other query parameters
 * (filters) and going back to the first page.
 */
class TableHeader
{
    public string $label;

    public ?string $field = null;

    public ?Sort $sort = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function isSortable(): bool
    {
        if (null === $this->field || null === $this->sort) {
            return false;
        }

        $route = $this->requestStack->getCurrentRequest()?->attributes->get('_route');

        return \is_string($route) && '' !== $route;
    }

    /**
     * 'ASC' or 'DESC' when this column is the sorted one, null otherwise.
     */
    public function currentDirection(): ?string
    {
        return null === $this->field ? null : $this->sort?->directionFor($this->field);
    }

    public function ariaSort(): ?string
    {
        return match ($this->currentDirection()) {
            'ASC' => 'ascending',
            'DESC' => 'descending',
            default => null,
        };
    }

    public function href(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || null === $this->field) {
            throw new \LogicException('A sortable header needs a request and a field.');
        }

        $routeParams = $request->attributes->get('_route_params', []);

        // Route parameters over the query: a filter named like a placeholder must not
        // change the list linked to. The sort over both, and back to the first page.
        $params = array_merge($request->query->all(), \is_array($routeParams) ? $routeParams : []);
        unset($params['page']);
        $params['sort'] = $this->field;
        $params['direction'] = 'DESC' === $this->currentDirection() ? 'asc' : 'desc';

        return $this->urlGenerator->generate((string) $request->attributes->get('_route'), $params);
    }
}
