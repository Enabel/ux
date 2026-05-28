<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Layout;

use Enabel\Ux\Component\Layout\EmailLayout;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class EmailLayoutTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount([]);

        $component->logo = $data['logo'];
        $component->address = $data['address'];
        $component->addressLine = $data['addressLine'];
        $component->noreply = $data['noreply'];
        $component->copyrightYear = $data['copyrightYear'];
        $component->copyrightHolder = $data['copyrightHolder'];

        $this->assertSame('@images/enabel-logo-email.png', $component->logo);
        $this->assertSame('Belgian Development Agency', $component->address);
        $this->assertSame('Rue Haute 147 - 1000 Brussels', $component->addressLine);
        $this->assertSame('Responses to this e-mail will not be read.', $component->noreply);
        $this->assertSame((int) date('Y'), $component->copyrightYear);
        $this->assertSame('Enabel', $component->copyrightHolder);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount([
            'logo' => '@images/custom-logo.png',
            'address' => 'Custom Agency',
            'addressLine' => 'Some Street 1 - 1000 City',
            'noreply' => 'Do not reply.',
            'copyrightYear' => 2020,
            'copyrightHolder' => 'Acme',
        ]);

        $this->assertSame('@images/custom-logo.png', $data['logo']);
        $this->assertSame('Custom Agency', $data['address']);
        $this->assertSame('Some Street 1 - 1000 City', $data['addressLine']);
        $this->assertSame('Do not reply.', $data['noreply']);
        $this->assertSame(2020, $data['copyrightYear']);
        $this->assertSame('Acme', $data['copyrightHolder']);
    }

    public function testCopyrightYearDefaultsToCurrentYearWhenNullPassed(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount(['copyrightYear' => null]);

        $this->assertSame((int) date('Y'), $data['copyrightYear']);
    }

    public function testCopyrightYearPreservesExplicitValue(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount(['copyrightYear' => 2010]);

        $this->assertSame(2010, $data['copyrightYear']);
    }

    public function testLogoCanBeNull(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount(['logo' => null]);

        $this->assertNull($data['logo']);
    }

    public function testNoreplyCanBeNull(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount(['noreply' => null]);

        $this->assertNull($data['noreply']);
    }

    public function testInvalidLogoTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new EmailLayout();
        $component->preMount(['logo' => 123]);
    }

    public function testInvalidAddressTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new EmailLayout();
        $component->preMount(['address' => 123]);
    }

    public function testInvalidCopyrightYearTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new EmailLayout();
        $component->preMount(['copyrightYear' => '2024']);
    }

    public function testInvalidCopyrightHolderTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new EmailLayout();
        $component->preMount(['copyrightHolder' => null]);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new EmailLayout();
        $data = $component->preMount(['custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
