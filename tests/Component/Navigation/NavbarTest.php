<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\Navbar;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class NavbarTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Navbar();
        $data = $component->preMount([]);

        $component->logo = $data['logo'];
        $component->name = $data['name'];
        $component->link = $data['link'];
        $component->theme = $data['theme'];
        $component->expand = $data['expand'];
        $component->fixed = $data['fixed'];
        $component->fixedPosition = $data['fixedPosition'];

        $this->assertInstanceOf(Navbar::class, $component);
        $this->assertNull($component->logo);
        $this->assertNull($component->name);
        $this->assertEquals('/', $component->link);
        $this->assertEquals('light', $component->theme);
        $this->assertEquals('lg', $component->expand);
        $this->assertFalse($component->fixed);
        $this->assertEquals('top', $component->fixedPosition);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new Navbar();
        $data = $component->preMount([
            'logo' => '/images/logo.png',
            'name' => 'My App',
            'link' => '/home',
            'theme' => 'dark',
            'expand' => 'md',
            'fixed' => true,
            'fixedPosition' => 'bottom',
        ]);

        $component->logo = $data['logo'];
        $component->name = $data['name'];
        $component->link = $data['link'];
        $component->theme = $data['theme'];
        $component->expand = $data['expand'];
        $component->fixed = $data['fixed'];
        $component->fixedPosition = $data['fixedPosition'];

        $this->assertEquals('/images/logo.png', $component->logo);
        $this->assertEquals('My App', $component->name);
        $this->assertEquals('/home', $component->link);
        $this->assertEquals('dark', $component->theme);
        $this->assertEquals('md', $component->expand);
        $this->assertTrue($component->fixed);
        $this->assertEquals('bottom', $component->fixedPosition);
    }

    public function testComponentSupportsAllThemeValues(): void
    {
        $themes = ['light', 'dark'];

        foreach ($themes as $theme) {
            $component = new Navbar();
            $data = $component->preMount(['theme' => $theme]);

            $component->theme = $data['theme'];

            $this->assertEquals($theme, $component->theme);
        }
    }

    public function testComponentSupportsAllExpandValues(): void
    {
        $expandValues = ['sm', 'md', 'lg', 'xl', 'xxl', 'always', 'never'];

        foreach ($expandValues as $expand) {
            $component = new Navbar();
            $data = $component->preMount(['expand' => $expand]);

            $component->expand = $data['expand'];

            $this->assertEquals($expand, $component->expand);
        }
    }

    public function testComponentSupportsAllFixedPositionValues(): void
    {
        $positions = ['top', 'bottom'];

        foreach ($positions as $position) {
            $component = new Navbar();
            $data = $component->preMount(['fixedPosition' => $position]);

            $component->fixedPosition = $data['fixedPosition'];

            $this->assertEquals($position, $component->fixedPosition);
        }
    }

    public function testComponentThrowsExceptionForInvalidTheme(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Navbar();
        $component->preMount(['theme' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidExpand(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Navbar();
        $component->preMount(['expand' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidFixedPosition(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Navbar();
        $component->preMount(['fixedPosition' => 'invalid']);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $component = new Navbar();
        $result = $component->preMount(['name' => 'Test App']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('logo', $result);
        $this->assertArrayHasKey('link', $result);
        $this->assertArrayHasKey('theme', $result);
        $this->assertEquals('Test App', $result['name']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Navbar();
        $result = $component->preMount(['name' => 'Test', 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }
}
