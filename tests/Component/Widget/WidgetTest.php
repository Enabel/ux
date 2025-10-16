<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Widget;

use Enabel\Ux\Component\Widget\Widget;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class WidgetTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Widget();
        $data = $component->preMount(['label' => 'Test Widget']);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertInstanceOf(Widget::class, $component);
        $this->assertEquals('Test Widget', $component->label);
        $this->assertEquals('primary', $component->variant);
        $this->assertNull($component->icon);
        $this->assertNull($component->link);
        $this->assertNull($component->count);
    }

    public function testComponentCanBeInstantiatedWithCustomVariant(): void
    {
        $component = new Widget();
        $data = $component->preMount([
            'label' => 'Success Widget',
            'variant' => 'success',
            'count' => '42',
        ]);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertInstanceOf(Widget::class, $component);
        $this->assertEquals('Success Widget', $component->label);
        $this->assertEquals('success', $component->variant);
        $this->assertEquals('42', $component->count);
    }

    public function testComponentCanBeInstantiatedWithIcon(): void
    {
        $component = new Widget();
        $data = $component->preMount([
            'label' => 'Widget with Icon',
            'variant' => 'info',
            'icon' => 'lucide:star',
            'count' => '100',
        ]);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertInstanceOf(Widget::class, $component);
        $this->assertEquals('Widget with Icon', $component->label);
        $this->assertEquals('info', $component->variant);
        $this->assertEquals('lucide:star', $component->icon);
        $this->assertEquals('100', $component->count);
    }

    public function testComponentCanBeInstantiatedWithLink(): void
    {
        $component = new Widget();
        $data = $component->preMount([
            'label' => 'Clickable Widget',
            'variant' => 'primary',
            'link' => '/dashboard',
            'count' => '25',
        ]);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertInstanceOf(Widget::class, $component);
        $this->assertEquals('Clickable Widget', $component->label);
        $this->assertEquals('primary', $component->variant);
        $this->assertEquals('/dashboard', $component->link);
        $this->assertEquals('25', $component->count);
    }

    public function testComponentSupportsAllBootstrapVariants(): void
    {
        $variants = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        foreach ($variants as $variant) {
            $component = new Widget();
            $data = $component->preMount(['label' => 'Test', 'variant' => $variant]);

            $component->variant = $data['variant'];

            $this->assertEquals($variant, $component->variant);
        }
    }

    public function testComponentThrowsExceptionForInvalidVariant(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Widget();
        $component->preMount(['label' => 'Test', 'variant' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidLabelType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Widget();
        $component->preMount(['label' => 123, 'variant' => 'info']);
    }

    public function testComponentThrowsExceptionForInvalidVariantType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Widget();
        $component->preMount(['label' => 'Test', 'variant' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidIconType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Widget();
        $component->preMount(['label' => 'Test', 'icon' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidLinkType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Widget();
        $component->preMount(['label' => 'Test', 'link' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidCountType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Widget();
        $component->preMount(['label' => 'Test', 'count' => 123]);
    }

    public function testComponentCanHandleComplexCount(): void
    {
        $component = new Widget();
        $data = $component->preMount([
            'label' => 'Revenue',
            'variant' => 'success',
            'count' => '$45,123.50',
            'icon' => 'lucide:dollar-sign',
        ]);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertEquals('$45,123.50', $component->count);
        $this->assertEquals('Revenue', $component->label);
        $this->assertEquals('success', $component->variant);
        $this->assertEquals('lucide:dollar-sign', $component->icon);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $component = new Widget();
        $result = $component->preMount([
            'label' => 'Test Widget',
            'variant' => 'warning',
            'count' => '10',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('variant', $result);
        $this->assertArrayHasKey('icon', $result);
        $this->assertArrayHasKey('link', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals('Test Widget', $result['label']);
        $this->assertEquals('warning', $result['variant']);
        $this->assertEquals('10', $result['count']);
        $this->assertNull($result['icon']);
        $this->assertNull($result['link']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Widget();
        $result = $component->preMount(['label' => 'Test', 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }

    public function testComponentCanHandleAllParametersCombination(): void
    {
        $component = new Widget();
        $data = $component->preMount([
            'label' => 'Complete Widget',
            'variant' => 'primary',
            'icon' => 'lucide:star',
            'link' => '/projects',
            'count' => '1,234',
        ]);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertEquals('Complete Widget', $component->label);
        $this->assertEquals('primary', $component->variant);
        $this->assertEquals('lucide:star', $component->icon);
        $this->assertEquals('/projects', $component->link);
        $this->assertEquals('1,234', $component->count);
    }

    public function testComponentWithOnlyLabelParameter(): void
    {
        $component = new Widget();
        $data = $component->preMount(['label' => 'Simple Widget']);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertEquals('Simple Widget', $component->label);
        $this->assertEquals('primary', $component->variant);
        $this->assertNull($component->icon);
        $this->assertNull($component->link);
        $this->assertNull($component->count);
    }

    public function testComponentWithEmptyLabel(): void
    {
        $component = new Widget();
        $data = $component->preMount(['label' => '']);

        $component->label = $data['label'];
        $component->variant = $data['variant'];

        $this->assertEquals('', $component->label);
        $this->assertEquals('primary', $component->variant);
    }

    public function testComponentWithoutCount(): void
    {
        $component = new Widget();
        $data = $component->preMount([
            'label' => 'Status Widget',
            'variant' => 'success',
            'icon' => 'lucide:check-circle',
        ]);

        $component->label = $data['label'];
        $component->variant = $data['variant'];
        $component->icon = $data['icon'];
        $component->link = $data['link'];
        $component->count = $data['count'];

        $this->assertEquals('Status Widget', $component->label);
        $this->assertEquals('success', $component->variant);
        $this->assertEquals('lucide:check-circle', $component->icon);
        $this->assertNull($component->count);
    }
}
