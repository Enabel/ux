<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\SidebarSection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class SidebarSectionTest extends TestCase
{
    public function testLabelIsKept(): void
    {
        $data = (new SidebarSection())->preMount(['label' => 'Administration']);

        $this->assertSame('Administration', $data['label']);
    }

    public function testLabelIsRequired(): void
    {
        $this->expectException(MissingOptionsException::class);

        (new SidebarSection())->preMount([]);
    }

    public function testLabelMustBeAString(): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new SidebarSection())->preMount(['label' => 5]);
    }
}
