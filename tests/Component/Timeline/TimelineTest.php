<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Timeline;

use Enabel\Ux\Component\Timeline\Timeline;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class TimelineTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Timeline();
        $data = $component->preMount([]);

        $component->title = $data['title'];
        $component->items = $data['items'];
        $component->showColors = $data['showColors'];

        $this->assertInstanceOf(Timeline::class, $component);
        $this->assertNull($component->title);
        $this->assertEquals([], $component->items);
        $this->assertFalse($component->showColors);
    }

    public function testComponentCanBeInstantiatedWithBasicTimeline(): void
    {
        $items = [
            ['date' => '2024-01-10', 'label' => 'Start'],
            ['date' => '2024-03-20', 'label' => 'Milestone'],
        ];

        $component = new Timeline();
        $data = $component->preMount([
            'title' => 'Project Timeline',
            'items' => $items,
        ]);

        $component->title = $data['title'];
        $component->items = $data['items'];

        $this->assertInstanceOf(Timeline::class, $component);
        $this->assertEquals('Project Timeline', $component->title);
        $this->assertCount(2, $component->items);
        $this->assertEquals('2024-01-10', $component->items[0]['date']);
        $this->assertEquals('Start', $component->items[0]['label']);
    }

    public function testComponentCanBeInstantiatedWithCompleteItems(): void
    {
        $items = [
            [
                'date' => '2024-01-10',
                'label' => 'Kick-off',
                'description' => 'Project started',
                'icon' => 'lucide:flag',
                'color' => 'success',
            ],
            [
                'date' => '2024-03-20',
                'label' => 'First delivery',
                'description' => 'Initial milestone reached',
                'icon' => 'lucide:package',
                'color' => 'info',
            ],
        ];

        $component = new Timeline();
        $data = $component->preMount([
            'title' => 'Project milestones',
            'items' => $items,
            'showColors' => true,
        ]);

        $component->title = $data['title'];
        $component->items = $data['items'];
        $component->showColors = $data['showColors'];

        $this->assertEquals('Project milestones', $component->title);
        $this->assertTrue($component->showColors);
        $this->assertCount(2, $component->items);

        $firstItem = $component->items[0];
        $this->assertEquals('2024-01-10', $firstItem['date']);
        $this->assertEquals('Kick-off', $firstItem['label']);
        $this->assertEquals('Project started', $firstItem['description']);
        $this->assertEquals('lucide:flag', $firstItem['icon']);
        $this->assertEquals('success', $firstItem['color']);
    }

    public function testComponentSupportsAllBootstrapColors(): void
    {
        $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        foreach ($colors as $color) {
            $items = [['date' => '2024-01-01', 'label' => 'Test', 'color' => $color]];

            $component = new Timeline();
            $data = $component->preMount(['items' => $items]);

            $component->items = $data['items'];

            $this->assertEquals($color, $component->items[0]['color']);
        }
    }

    public function testComponentThrowsExceptionForInvalidColor(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $items = [['date' => '2024-01-01', 'label' => 'Test', 'color' => 'invalid']];

        $component = new Timeline();
        $component->preMount(['items' => $items]);
    }

    public function testComponentThrowsExceptionForInvalidTitleType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Timeline();
        $component->preMount(['title' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidItemsType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Timeline();
        $component->preMount(['items' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidShowColorsType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Timeline();
        $component->preMount(['showColors' => 'yes']);
    }

    public function testComponentThrowsExceptionForNonArrayItem(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each item must be an array.');

        $items = ['invalid_item'];

        $component = new Timeline();
        $component->preMount(['items' => $items]);
    }

    public function testComponentCanHandleItemsWithMinimalData(): void
    {
        $items = [
            ['label' => 'Simple event'],
            ['date' => '2024-01-01'],
        ];

        $component = new Timeline();
        $data = $component->preMount(['items' => $items]);

        $component->items = $data['items'];

        $this->assertCount(2, $component->items);
        $this->assertEquals('Simple event', $component->items[0]['label']);
        $this->assertNull($component->items[0]['date']);
        $this->assertEquals('2024-01-01', $component->items[1]['date']);
        $this->assertNull($component->items[1]['label']);
    }

    public function testComponentCanHandleEmptyItems(): void
    {
        $component = new Timeline();
        $data = $component->preMount(['items' => []]);

        $component->items = $data['items'];

        $this->assertCount(0, $component->items);
    }

    public function testComponentSupportsNullColorValue(): void
    {
        $items = [['date' => '2024-01-01', 'label' => 'Test', 'color' => null]];

        $component = new Timeline();
        $data = $component->preMount(['items' => $items]);

        $component->items = $data['items'];

        $this->assertNull($component->items[0]['color']);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $items = [['date' => '2024-01-01', 'label' => 'Test Event']];

        $component = new Timeline();
        $result = $component->preMount([
            'title' => 'Test Timeline',
            'items' => $items,
            'showColors' => true,
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('showColors', $result);
        $this->assertEquals('Test Timeline', $result['title']);
        $this->assertTrue($result['showColors']);
        $this->assertCount(1, $result['items']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Timeline();
        $result = $component->preMount([
            'title' => 'Test',
            'custom_attribute' => 'value',
        ]);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }

    public function testComponentNormalizesItemDefaults(): void
    {
        $items = [['label' => 'Test Event']];

        $component = new Timeline();
        $data = $component->preMount(['items' => $items]);

        $component->items = $data['items'];
        $item = $component->items[0];

        $this->assertArrayHasKey('date', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('description', $item);
        $this->assertArrayHasKey('icon', $item);
        $this->assertArrayHasKey('color', $item);

        $this->assertNull($item['date']);
        $this->assertEquals('Test Event', $item['label']);
        $this->assertNull($item['description']);
        $this->assertNull($item['icon']);
        $this->assertNull($item['color']);
    }
}
