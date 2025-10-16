<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Callout;

use Enabel\Ux\Component\Callout\Callout;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CalloutTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Callout();
        $data = $component->preMount(['message' => 'Test message']);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];
        $component->size = $data['size'];

        $this->assertInstanceOf(Callout::class, $component);
        $this->assertEquals('Test message', $component->message);
        $this->assertEquals('info', $component->type);
        $this->assertNull($component->title);
        $this->assertNull($component->icon);
        $this->assertNull($component->size);
    }

    public function testComponentCanBeInstantiatedWithCustomType(): void
    {
        $component = new Callout();
        $data = $component->preMount([
            'title' => 'Success Title',
            'message' => 'Success message',
            'type' => 'success'
        ]);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];
        $component->size = $data['size'];

        $this->assertInstanceOf(Callout::class, $component);
        $this->assertEquals('Success Title', $component->title);
        $this->assertEquals('Success message', $component->message);
        $this->assertEquals('success', $component->type);
    }

    public function testComponentCanBeInstantiatedWithIcon(): void
    {
        $component = new Callout();
        $data = $component->preMount([
            'title' => 'Information',
            'message' => 'This is important information.',
            'type' => 'info',
            'icon' => 'lucide:info'
        ]);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];
        $component->size = $data['size'];

        $this->assertInstanceOf(Callout::class, $component);
        $this->assertEquals('Information', $component->title);
        $this->assertEquals('This is important information.', $component->message);
        $this->assertEquals('info', $component->type);
        $this->assertEquals('lucide:info', $component->icon);
    }

    public function testComponentCanBeInstantiatedWithSmallSize(): void
    {
        $component = new Callout();
        $data = $component->preMount([
            'message' => 'Small callout',
            'size' => 'sm'
        ]);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];
        $component->size = $data['size'];

        $this->assertInstanceOf(Callout::class, $component);
        $this->assertEquals('Small callout', $component->message);
        $this->assertEquals('sm', $component->size);
    }

    public function testComponentSupportsAllBootstrapCalloutTypes(): void
    {
        $types = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        foreach ($types as $type) {
            $component = new Callout();
            $data = $component->preMount(['message' => 'Test', 'type' => $type]);

            $component->type = $data['type'];

            $this->assertEquals($type, $component->type);
        }
    }

    public function testComponentThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Callout();
        $component->preMount(['message' => 'Test', 'type' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidMessageType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Callout();
        $component->preMount(['message' => 123, 'type' => 'info']);
    }

    public function testComponentThrowsExceptionForInvalidTitleType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Callout();
        $component->preMount(['title' => 123, 'message' => 'Test']);
    }

    public function testComponentThrowsExceptionForInvalidIconType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Callout();
        $component->preMount(['icon' => 123, 'message' => 'Test']);
    }

    public function testComponentThrowsExceptionForInvalidSize(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Callout();
        $component->preMount(['size' => 'large', 'message' => 'Test']);
    }

    public function testComponentCanHandleDangerType(): void
    {
        $component = new Callout();
        $data = $component->preMount([
            'title' => 'Error',
            'message' => 'An error occurred',
            'type' => 'danger',
            'icon' => 'lucide:alert-circle'
        ]);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];

        $this->assertEquals('danger', $component->type);
        $this->assertEquals('An error occurred', $component->message);
        $this->assertEquals('Error', $component->title);
        $this->assertEquals('lucide:alert-circle', $component->icon);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $component = new Callout();
        $result = $component->preMount([
            'title' => 'Test Title',
            'message' => 'Test message',
            'type' => 'warning'
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('icon', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertEquals('Test Title', $result['title']);
        $this->assertEquals('Test message', $result['message']);
        $this->assertEquals('warning', $result['type']);
        $this->assertNull($result['icon']);
        $this->assertNull($result['size']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Callout();
        $result = $component->preMount(['message' => 'Test', 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }

    public function testComponentCanHandleAllParametersCombination(): void
    {
        $component = new Callout();
        $data = $component->preMount([
            'title' => 'Complete Callout',
            'message' => 'This callout has all parameters set.',
            'type' => 'primary',
            'icon' => 'lucide:star',
            'size' => 'sm'
        ]);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];
        $component->size = $data['size'];

        $this->assertEquals('Complete Callout', $component->title);
        $this->assertEquals('This callout has all parameters set.', $component->message);
        $this->assertEquals('primary', $component->type);
        $this->assertEquals('lucide:star', $component->icon);
        $this->assertEquals('sm', $component->size);
    }

    public function testComponentWithOnlyMessageParameter(): void
    {
        $component = new Callout();
        $data = $component->preMount(['message' => 'Simple message']);

        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->type = $data['type'];
        $component->icon = $data['icon'];
        $component->size = $data['size'];

        $this->assertNull($component->title);
        $this->assertEquals('Simple message', $component->message);
        $this->assertEquals('info', $component->type);
        $this->assertNull($component->icon);
        $this->assertNull($component->size);
    }
}