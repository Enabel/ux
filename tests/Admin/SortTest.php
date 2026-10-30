<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Admin;

use Enabel\Ux\Admin\Sort;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SortTest extends TestCase
{
    private const array ALLOWED = ['occurredAt' => 'e.occurredAt', 'actor' => 'e.actor'];

    public function testAllowedFieldAndDirectionAreKept(): void
    {
        $sort = Sort::fromRequest(new Request(['sort' => 'actor', 'direction' => 'asc']), self::ALLOWED, 'occurredAt');

        $this->assertSame('actor', $sort->field);
        $this->assertSame('ASC', $sort->direction);
        $this->assertSame('e.actor', $sort->path(self::ALLOWED));
    }

    public function testUnknownFieldFallsBackToTheDefault(): void
    {
        $sort = Sort::fromRequest(new Request(['sort' => 'password']), self::ALLOWED, 'occurredAt');

        $this->assertSame('occurredAt', $sort->field);
        $this->assertSame('e.occurredAt', $sort->path(self::ALLOWED));
    }

    public function testNonStringFieldFallsBackToTheDefault(): void
    {
        $sort = Sort::fromRequest(new Request(['sort' => ['actor']]), self::ALLOWED, 'occurredAt');

        $this->assertSame('occurredAt', $sort->field);
    }

    public function testUnknownDirectionFallsBackToTheDefault(): void
    {
        $this->assertSame('DESC', Sort::fromRequest(new Request(['direction' => 'sideways']), self::ALLOWED, 'occurredAt')->direction);
        $this->assertSame('ASC', Sort::fromRequest(new Request(), self::ALLOWED, 'occurredAt', 'ASC')->direction);
    }

    public function testDefaultFieldMustBeAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Sort::fromRequest(new Request(), self::ALLOWED, 'password');
    }

    public function testPathOfAFieldOutsideTheAllowListIsRefused(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Sort('password', 'ASC'))->path(self::ALLOWED);
    }

    public function testDirectionForAnotherFieldIsNull(): void
    {
        $sort = new Sort('actor', 'ASC');

        $this->assertSame('ASC', $sort->directionFor('actor'));
        $this->assertNull($sort->directionFor('occurredAt'));
    }
}
