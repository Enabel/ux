<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests;

use Enabel\Ux\EnabelUxBundle;
use PHPUnit\Framework\TestCase;

class EnabelUxBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new EnabelUxBundle();
        $this->assertInstanceOf(EnabelUxBundle::class, $bundle);
    }

    public function testGetPath(): void
    {
        $bundle = new EnabelUxBundle();
        $path = $bundle->getPath();

        $this->assertIsString($path);
        $this->assertDirectoryExists($path);
        $this->assertDirectoryExists($path.'/src');
    }

    public function testBundleNameIsCorrect(): void
    {
        $bundle = new EnabelUxBundle();

        // Test that the bundle can be used in a container
        $reflection = new \ReflectionClass($bundle);
        $this->assertEquals('EnabelUxBundle', $reflection->getShortName());
        $this->assertEquals('Enabel\Ux', $reflection->getNamespaceName());
    }
}
