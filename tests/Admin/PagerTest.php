<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Admin;

use Enabel\Ux\Admin\Pager;
use Pagerfanta\Adapter\ArrayAdapter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagerTest extends TestCase
{
    public function testRequestedPageIsUsed(): void
    {
        $pager = Pager::fromAdapter(new ArrayAdapter([1, 2, 3, 4, 5]), new Request(['page' => '2']), 2);

        $this->assertSame(2, $pager->getCurrentPage());
        $this->assertSame(2, $pager->getMaxPerPage());
        $this->assertSame([3, 4], iterator_to_array($pager->getCurrentPageResults(), false));
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function garbagePages(): iterable
    {
        yield 'missing' => [null];
        yield 'text' => ['abc'];
        yield 'zero' => ['0'];
        yield 'negative' => ['-3'];
        yield 'array' => [['2']];
    }

    #[DataProvider('garbagePages')]
    public function testGarbagePageMeansTheFirstPage(mixed $page): void
    {
        $query = null === $page ? [] : ['page' => $page];

        $this->assertSame(1, Pager::fromAdapter(new ArrayAdapter([1, 2, 3]), new Request($query), 2)->getCurrentPage());
    }

    public function testPagePastTheEndIsNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        Pager::fromAdapter(new ArrayAdapter([1, 2, 3]), new Request(['page' => '9999']), 2);
    }
}
