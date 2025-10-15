<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Alert;

use Enabel\Ux\Component\Alert\Alert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class AlertTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Alert();
        $data = $component->preMount(['text' => 'Test message']);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];

        $this->assertInstanceOf(Alert::class, $component);
        $this->assertEquals('Test message', $component->text);
        $this->assertEquals('info', $component->type);
        $this->assertFalse($component->dismissible);
    }

    public function testComponentCanBeInstantiatedWithCustomType(): void
    {
        $component = new Alert();
        $data = $component->preMount(['text' => 'Success message', 'type' => 'success']);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];

        $this->assertInstanceOf(Alert::class, $component);
        $this->assertEquals('Success message', $component->text);
        $this->assertEquals('success', $component->type);
        $this->assertFalse($component->dismissible);
    }

    public function testComponentCanBeInstantiatedWithDismissibleSetToTrue(): void
    {
        $component = new Alert();
        $data = $component->preMount(['text' => 'Warning message', 'type' => 'warning', 'dismissible' => true]);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];

        $this->assertInstanceOf(Alert::class, $component);
        $this->assertEquals('Warning message', $component->text);
        $this->assertEquals('warning', $component->type);
        $this->assertTrue($component->dismissible);
    }

    public function testComponentSupportsAllBootstrapAlertTypes(): void
    {
        $types = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        foreach ($types as $type) {
            $component = new Alert();
            $data = $component->preMount(['text' => 'Test', 'type' => $type]);

            $component->type = $data['type'];

            $this->assertEquals($type, $component->type);
        }
    }

    public function testComponentThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Alert();
        $component->preMount(['text' => 'Test', 'type' => 'invalid']);
    }

    public function testComponentThrowsExceptionForInvalidTextType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Alert();
        $component->preMount(['text' => 123, 'type' => 'info']);
    }

    public function testComponentThrowsExceptionForInvalidDismissibleType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Alert();
        $component->preMount(['text' => 'Test', 'dismissible' => 'yes']);
    }

    public function testComponentCanHandlePrimaryType(): void
    {
        $component = new Alert();
        $data = $component->preMount(['text' => 'Primary alert', 'type' => 'primary']);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];

        $this->assertEquals('primary', $component->type);
        $this->assertEquals('Primary alert', $component->text);
    }

    public function testComponentCanHandleDangerType(): void
    {
        $component = new Alert();
        $data = $component->preMount(['text' => 'Error occurred', 'type' => 'danger', 'dismissible' => true]);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];

        $this->assertEquals('danger', $component->type);
        $this->assertEquals('Error occurred', $component->text);
        $this->assertTrue($component->dismissible);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $component = new Alert();
        $result = $component->preMount(['text' => 'Test message', 'type' => 'success']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('text', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('dismissible', $result);
        $this->assertEquals('Test message', $result['text']);
        $this->assertEquals('success', $result['type']);
        $this->assertFalse($result['dismissible']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Alert();
        $result = $component->preMount(['text' => 'Test', 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }
}
