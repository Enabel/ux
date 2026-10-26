<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\SidebarItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SidebarItemTest extends TestCase
{
    private function createItem(?string $route = null): SidebarItem
    {
        $requestStack = new RequestStack();
        if (null !== $route) {
            $request = new Request();
            $request->attributes->set('_route', $route);
            $requestStack->push($request);
        }

        return new SidebarItem($requestStack);
    }

    public function testActiveWhenTheCurrentRouteStartsWithThePrefix(): void
    {
        $data = $this->createItem('admin.responses.delegate')->preMount([
            'label' => 'Responses',
            'link' => '/admin/responses',
            'activeRoutePrefix' => 'admin.responses',
        ]);

        $this->assertTrue($data['active']);
    }

    public function testInactiveForAnotherRoute(): void
    {
        $data = $this->createItem('admin.security_events')->preMount([
            'label' => 'Responses',
            'link' => '/admin/responses',
            'activeRoutePrefix' => 'admin.responses',
        ]);

        $this->assertFalse($data['active']);
    }

    public function testExplicitActiveWins(): void
    {
        $data = $this->createItem('admin.responses.delegate')->preMount([
            'label' => 'Responses',
            'link' => '/admin/responses',
            'activeRoutePrefix' => 'admin.responses',
            'active' => false,
        ]);

        $this->assertFalse($data['active']);
    }

    public function testNoPrefixMeansInactive(): void
    {
        $data = $this->createItem('admin.responses')->preMount([
            'label' => 'Responses',
            'link' => '/admin/responses',
        ]);

        $this->assertFalse($data['active']);
    }

    public function testInactiveWithoutRequest(): void
    {
        $data = $this->createItem()->preMount([
            'label' => 'Responses',
            'link' => '/admin/responses',
            'activeRoutePrefix' => 'admin.responses',
        ]);

        $this->assertFalse($data['active']);
    }
}
