<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Admin;

use Symfony\Component\HttpFoundation\Request;

/**
 * Sort requested by a list (`sort` and `direction` query parameters),
 * restricted to an allow-list of fields.
 *
 * The request value is only ever used as a key of the allow-list: the path
 * handed to ORDER BY comes from the controller, never from the request.
 *
 *     $sort = Sort::fromRequest($request, self::SORTABLE, 'createdAt');
 *     $qb->orderBy($sort->path(self::SORTABLE), $sort->direction);
 */
final readonly class Sort
{
    public function __construct(
        public string $field,
        public string $direction,
    ) {
    }

    /**
     * @param array<string, string> $allowed query value => path to sort on (e.g. a DQL path)
     */
    public static function fromRequest(Request $request, array $allowed, string $defaultField, string $defaultDirection = 'DESC'): self
    {
        if (!\array_key_exists($defaultField, $allowed)) {
            throw new \InvalidArgumentException(\sprintf('The default sort field "%s" is not in the allowed fields.', $defaultField));
        }

        // all(): get() answers ?sort[]=x with a 400, where a default sort is expected.
        $query = $request->query->all();

        $field = $query['sort'] ?? null;
        if (!\is_string($field) || !\array_key_exists($field, $allowed)) {
            $field = $defaultField;
        }

        $direction = $query['direction'] ?? null;
        $direction = match (\is_string($direction) ? strtolower($direction) : null) {
            'asc' => 'ASC',
            'desc' => 'DESC',
            default => $defaultDirection,
        };

        return new self($field, $direction);
    }

    /**
     * @param array<string, string> $allowed query value => path to sort on
     */
    public function path(array $allowed): string
    {
        return $allowed[$this->field] ?? throw new \InvalidArgumentException(\sprintf('The sort field "%s" is not in the allowed fields.', $this->field));
    }

    /**
     * 'ASC' or 'DESC' when this field is the sorted one, null otherwise.
     */
    public function directionFor(string $field): ?string
    {
        return $field === $this->field ? $this->direction : null;
    }
}
