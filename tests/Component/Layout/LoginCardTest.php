<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Layout;

use Enabel\Ux\Component\Layout\LoginCard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class LoginCardTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new LoginCard();
        $data = $component->preMount([]);

        $component->logo = $data['logo'];
        $component->title = $data['title'];
        $component->background = $data['background'];
        $component->backgroundImage = $data['backgroundImage'];
        $component->size = $data['size'];

        $this->assertNull($component->logo);
        $this->assertNull($component->title);
        $this->assertSame('primary', $component->background);
        $this->assertNull($component->backgroundImage);
        $this->assertSame('md', $component->size);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new LoginCard();
        $data = $component->preMount([
            'logo' => 'images/enabel-logo.png',
            'title' => 'Sign in',
            'background' => 'dark',
            'backgroundImage' => 'images/photo.jpg',
            'size' => 'lg',
        ]);

        $this->assertSame('images/enabel-logo.png', $data['logo']);
        $this->assertSame('Sign in', $data['title']);
        $this->assertSame('dark', $data['background']);
        $this->assertSame('images/photo.jpg', $data['backgroundImage']);
        $this->assertSame('lg', $data['size']);
    }

    public function testAllValidBackgrounds(): void
    {
        $component = new LoginCard();
        foreach (['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'] as $bg) {
            $data = $component->preMount(['background' => $bg]);
            $this->assertSame($bg, $data['background']);
        }
    }

    public function testAllValidSizes(): void
    {
        $component = new LoginCard();
        foreach (['sm', 'md', 'lg', 'xl'] as $size) {
            $data = $component->preMount(['size' => $size]);
            $this->assertSame($size, $data['size']);
        }
    }

    public function testInvalidBackgroundThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new LoginCard();
        $component->preMount(['background' => 'rainbow']);
    }

    public function testInvalidSizeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new LoginCard();
        $component->preMount(['size' => 'xxl']);
    }

    public function testInvalidLogoTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new LoginCard();
        $component->preMount(['logo' => 123]);
    }

    public function testInvalidTitleTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new LoginCard();
        $component->preMount(['title' => true]);
    }

    public function testInvalidBackgroundImageTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new LoginCard();
        $component->preMount(['backgroundImage' => 42]);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new LoginCard();
        $data = $component->preMount(['custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
