<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Layout;

use Enabel\Ux\Component\Layout\AdminLayout;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class AdminLayoutTest extends TestCase
{
    public function testDefaults(): void
    {
        $data = (new AdminLayout())->preMount(['title' => 'Users']);

        $this->assertSame('Users', $data['title']);
        $this->assertSame([], $data['breadcrumb']);
    }

    public function testBreadcrumbIsKept(): void
    {
        $breadcrumb = [['label' => 'Admin', 'link' => '/admin'], ['label' => 'Users']];
        $data = (new AdminLayout())->preMount(['title' => 'Users', 'breadcrumb' => $breadcrumb]);

        $this->assertSame($breadcrumb, $data['breadcrumb']);
    }

    public function testBreadcrumbMustBeAList(): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new AdminLayout())->preMount(['title' => 'Users', 'breadcrumb' => ['label' => 'Admin']]);
    }

    public function testTitleIsRequired(): void
    {
        $this->expectException(MissingOptionsException::class);

        (new AdminLayout())->preMount([]);
    }
}
