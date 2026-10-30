<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Admin;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The pager of a list, on the `page` query parameter.
 *
 *     $pager = Pager::fromAdapter(new QueryAdapter($qb), $request, 50);
 */
final class Pager
{
    /**
     * A garbage page number means the first page; a page past the last one is a 404.
     *
     * @template T
     *
     * @param AdapterInterface<T> $adapter
     *
     * @return Pagerfanta<T>
     */
    public static function fromAdapter(AdapterInterface $adapter, Request $request, int $maxPerPage): Pagerfanta
    {
        // all(): get() answers ?page[]=2 with a 400, where the first page is expected.
        $page = filter_var($request->query->all()['page'] ?? null, \FILTER_VALIDATE_INT);
        if (false === $page || $page < 1) {
            $page = 1;
        }

        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($maxPerPage);

        try {
            $pager->setCurrentPage($page);
        } catch (OutOfRangeCurrentPageException) {
            throw new NotFoundHttpException('Page not found.');
        }

        return $pager;
    }
}
