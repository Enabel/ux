<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\Tab;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class TabTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Tab();
        $data = $component->preMount([]);

        $component->tabs = $data['tabs'];
        $component->activeTab = $data['activeTab'];
        $component->variant = $data['variant'];
        $component->fill = $data['fill'];
        $component->justified = $data['justified'];
        $component->alignment = $data['alignment'];

        $this->assertInstanceOf(Tab::class, $component);
        $this->assertEquals([], $component->tabs);
        $this->assertNull($component->activeTab);
        $this->assertEquals('tabs', $component->variant);
        $this->assertFalse($component->fill);
        $this->assertFalse($component->justified);
        $this->assertEquals('start', $component->alignment);
    }

    public function testComponentCanBeInstantiatedWithTabs(): void
    {
        $tabs = [
            ['id' => 'home', 'label' => 'Home', 'link' => '/home'],
            ['id' => 'profile', 'label' => 'Profile', 'link' => '/profile'],
        ];

        $component = new Tab();
        $data = $component->preMount(['tabs' => $tabs, 'activeTab' => 'home']);

        $component->tabs = $data['tabs'];
        $component->activeTab = $data['activeTab'];

        $this->assertInstanceOf(Tab::class, $component);
        $this->assertEquals($tabs, $component->tabs);
        $this->assertEquals('home', $component->activeTab);
    }

    public function testComponentSupportsTabsVariant(): void
    {
        $component = new Tab();
        $data = $component->preMount(['variant' => 'tabs']);

        $component->variant = $data['variant'];

        $this->assertEquals('tabs', $component->variant);
    }

    public function testComponentSupportsPillsVariant(): void
    {
        $component = new Tab();
        $data = $component->preMount(['variant' => 'pills']);

        $component->variant = $data['variant'];

        $this->assertEquals('pills', $component->variant);
    }

    public function testComponentThrowsExceptionForInvalidVariant(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Tab();
        $component->preMount(['variant' => 'invalid']);
    }

    public function testComponentCanBeInstantiatedWithFillEnabled(): void
    {
        $component = new Tab();
        $data = $component->preMount(['fill' => true]);

        $component->fill = $data['fill'];

        $this->assertTrue($component->fill);
    }

    public function testComponentCanBeInstantiatedWithJustifiedEnabled(): void
    {
        $component = new Tab();
        $data = $component->preMount(['justified' => true]);

        $component->justified = $data['justified'];

        $this->assertTrue($component->justified);
    }

    public function testComponentSupportsStartAlignment(): void
    {
        $component = new Tab();
        $data = $component->preMount(['alignment' => 'start']);

        $component->alignment = $data['alignment'];

        $this->assertEquals('start', $component->alignment);
    }

    public function testComponentSupportsCenterAlignment(): void
    {
        $component = new Tab();
        $data = $component->preMount(['alignment' => 'center']);

        $component->alignment = $data['alignment'];

        $this->assertEquals('center', $component->alignment);
    }

    public function testComponentSupportsEndAlignment(): void
    {
        $component = new Tab();
        $data = $component->preMount(['alignment' => 'end']);

        $component->alignment = $data['alignment'];

        $this->assertEquals('end', $component->alignment);
    }

    public function testComponentThrowsExceptionForInvalidAlignment(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Tab();
        $component->preMount(['alignment' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidTabsType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Tab();
        $component->preMount(['tabs' => 'not-an-array']);
    }

    public function testComponentThrowsExceptionForInvalidActiveTabType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Tab();
        $component->preMount(['activeTab' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidFillType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Tab();
        $component->preMount(['fill' => 'yes']);
    }

    public function testComponentThrowsExceptionForInvalidJustifiedType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Tab();
        $component->preMount(['justified' => 'yes']);
    }

    public function testComponentCanHandleComplexConfiguration(): void
    {
        $tabs = [
            ['id' => 'tab1', 'label' => 'Tab 1', 'link' => '/content1'],
            ['id' => 'tab2', 'label' => 'Tab 2', 'link' => '/content2'],
            ['id' => 'tab3', 'label' => 'Tab 3', 'link' => '/content3'],
        ];

        $component = new Tab();
        $data = $component->preMount([
            'tabs' => $tabs,
            'activeTab' => 'tab2',
            'activeContent' => '<p>Content for Tab 2</p>',
            'variant' => 'pills',
            'fill' => true,
            'alignment' => 'center',
        ]);

        $component->tabs = $data['tabs'];
        $component->activeTab = $data['activeTab'];
        $component->activeContent = $data['activeContent'];
        $component->variant = $data['variant'];
        $component->fill = $data['fill'];
        $component->justified = $data['justified'];
        $component->alignment = $data['alignment'];

        $this->assertEquals($tabs, $component->tabs);
        $this->assertEquals('tab2', $component->activeTab);
        $this->assertEquals('<p>Content for Tab 2</p>', $component->activeContent);
        $this->assertEquals('pills', $component->variant);
        $this->assertTrue($component->fill);
        $this->assertFalse($component->justified);
        $this->assertEquals('center', $component->alignment);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $tabs = [
            ['id' => 'home', 'label' => 'Home'],
        ];

        $component = new Tab();
        $result = $component->preMount(['tabs' => $tabs, 'activeTab' => 'home']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('tabs', $result);
        $this->assertArrayHasKey('activeTab', $result);
        $this->assertArrayHasKey('variant', $result);
        $this->assertArrayHasKey('fill', $result);
        $this->assertArrayHasKey('justified', $result);
        $this->assertArrayHasKey('alignment', $result);
        $this->assertEquals($tabs, $result['tabs']);
        $this->assertEquals('home', $result['activeTab']);
        $this->assertEquals('tabs', $result['variant']);
        $this->assertFalse($result['fill']);
        $this->assertFalse($result['justified']);
        $this->assertEquals('start', $result['alignment']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Tab();
        $result = $component->preMount(['tabs' => [], 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }

    public function testComponentCanHandleEmptyTabs(): void
    {
        $component = new Tab();
        $data = $component->preMount(['tabs' => []]);

        $component->tabs = $data['tabs'];

        $this->assertInstanceOf(Tab::class, $component);
        $this->assertIsArray($component->tabs);
        $this->assertEmpty($component->tabs);
    }

    public function testComponentCanHandleTabsWithLinks(): void
    {
        $tabs = [
            ['id' => 'page1', 'label' => 'Page 1', 'link' => '/page1'],
            ['id' => 'page2', 'label' => 'Page 2', 'link' => '/page2'],
        ];

        $component = new Tab();
        $data = $component->preMount(['tabs' => $tabs, 'activeTab' => 'page1']);

        $component->tabs = $data['tabs'];
        $component->activeTab = $data['activeTab'];

        $this->assertEquals($tabs, $component->tabs);
        $this->assertEquals('page1', $component->activeTab);
    }
}
