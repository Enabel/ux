<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component;

use Enabel\Ux\Component\Modal;
use PHPUnit\Framework\TestCase;

class ModalTest extends TestCase
{
    public function testModalCanBeInstantiated(): void
    {
        $modal = new Modal();
        $this->assertInstanceOf(Modal::class, $modal);
    }
}
