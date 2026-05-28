<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\ImpersonateBanner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ImpersonateBannerTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithRequiredParameters(): void
    {
        $component = new ImpersonateBanner();
        $data = $component->preMount([
            'message' => 'Impersonating Jane Doe',
            'exitUrl' => '?_switch_user=_exit',
        ]);

        $component->message = $data['message'];
        $component->exitUrl = $data['exitUrl'];
        $component->exitLabel = $data['exitLabel'];
        $component->icon = $data['icon'];

        $this->assertSame('Impersonating Jane Doe', $component->message);
        $this->assertSame('?_switch_user=_exit', $component->exitUrl);
        $this->assertSame('Exit impersonation', $component->exitLabel);
        $this->assertSame('fa6-solid:user-secret', $component->icon);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new ImpersonateBanner();
        $data = $component->preMount([
            'message' => 'Vous incarnez Jane Doe',
            'exitUrl' => '/?_switch_user=_exit',
            'exitLabel' => 'Quitter',
            'icon' => 'fa6-solid:mask',
        ]);

        $this->assertSame('Vous incarnez Jane Doe', $data['message']);
        $this->assertSame('/?_switch_user=_exit', $data['exitUrl']);
        $this->assertSame('Quitter', $data['exitLabel']);
        $this->assertSame('fa6-solid:mask', $data['icon']);
    }

    public function testMissingMessageThrows(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = new ImpersonateBanner();
        $component->preMount(['exitUrl' => '?_switch_user=_exit']);
    }

    public function testMissingExitUrlThrows(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = new ImpersonateBanner();
        $component->preMount(['message' => 'Impersonating']);
    }

    public function testInvalidMessageTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateBanner();
        $component->preMount([
            'message' => 123,
            'exitUrl' => '?_switch_user=_exit',
        ]);
    }

    public function testInvalidExitUrlTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateBanner();
        $component->preMount([
            'message' => 'Impersonating',
            'exitUrl' => null,
        ]);
    }

    public function testInvalidExitLabelTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateBanner();
        $component->preMount([
            'message' => 'Impersonating',
            'exitUrl' => '?_switch_user=_exit',
            'exitLabel' => false,
        ]);
    }

    public function testInvalidIconTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateBanner();
        $component->preMount([
            'message' => 'Impersonating',
            'exitUrl' => '?_switch_user=_exit',
            'icon' => 42,
        ]);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new ImpersonateBanner();
        $data = $component->preMount([
            'message' => 'Impersonating',
            'exitUrl' => '?_switch_user=_exit',
            'custom_attribute' => 'value',
        ]);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
