<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Toast;

use Enabel\Ux\Component\Toast\Toast;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ToastTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Toast();
        $data = $component->preMount([]);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];
        $component->autohide = $data['autohide'];
        $component->delay = $data['delay'];
        $component->politeness = $data['politeness'];

        $this->assertNull($component->text);
        $this->assertSame('info', $component->type);
        $this->assertTrue($component->dismissible);
        $this->assertTrue($component->autohide);
        $this->assertSame(5000, $component->delay);
        $this->assertSame('polite', $component->politeness);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new Toast();
        $data = $component->preMount([
            'text' => 'Saved successfully',
            'type' => 'success',
            'dismissible' => false,
            'autohide' => false,
            'delay' => 10000,
            'politeness' => 'assertive',
        ]);

        $component->text = $data['text'];
        $component->type = $data['type'];
        $component->dismissible = $data['dismissible'];
        $component->autohide = $data['autohide'];
        $component->delay = $data['delay'];
        $component->politeness = $data['politeness'];

        $this->assertSame('Saved successfully', $component->text);
        $this->assertSame('success', $component->type);
        $this->assertFalse($component->dismissible);
        $this->assertFalse($component->autohide);
        $this->assertSame(10000, $component->delay);
        $this->assertSame('assertive', $component->politeness);
    }

    public function testInvalidTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Toast();
        $component->preMount(['type' => 'unknown']);
    }

    public function testValidTypes(): void
    {
        $component = new Toast();
        foreach (['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'] as $type) {
            $data = $component->preMount(['type' => $type]);
            $this->assertSame($type, $data['type']);
        }
    }

    public function testInvalidPolitenessThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Toast();
        $component->preMount(['politeness' => 'rude']);
    }

    public function testInvalidTextTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Toast();
        $component->preMount(['text' => 123]);
    }

    public function testInvalidDismissibleTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Toast();
        $component->preMount(['dismissible' => 'yes']);
    }

    public function testInvalidAutohideTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Toast();
        $component->preMount(['autohide' => 'yes']);
    }

    public function testInvalidDelayTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Toast();
        $component->preMount(['delay' => '5000']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Toast();
        $data = $component->preMount(['text' => 'Hi', 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
