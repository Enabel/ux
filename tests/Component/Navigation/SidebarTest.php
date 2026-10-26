<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\Sidebar;
use PHPUnit\Framework\TestCase;

class SidebarTest extends TestCase
{
    public function testDefaults(): void
    {
        $data = (new Sidebar())->preMount([]);

        $this->assertNull($data['label']);
    }

    public function testCustomLabel(): void
    {
        $data = (new Sidebar())->preMount(['label' => 'Administration']);

        $this->assertSame('Administration', $data['label']);
    }
}
