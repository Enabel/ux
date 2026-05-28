<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Layout;

use Enabel\Ux\Component\Layout\ErrorPage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ErrorPageTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithRequiredParameters(): void
    {
        $component = new ErrorPage();
        $data = $component->preMount([
            'statusCode' => 404,
            'title' => 'Not Found',
            'message' => 'The page does not exist.',
        ]);

        $component->statusCode = $data['statusCode'];
        $component->title = $data['title'];
        $component->message = $data['message'];
        $component->details = $data['details'];
        $component->backUrl = $data['backUrl'];
        $component->backLabel = $data['backLabel'];
        $component->symbol = $data['symbol'];
        $component->logo = $data['logo'];
        $component->locale = $data['locale'];
        $component->appName = $data['appName'];
        $component->stylesheet = $data['stylesheet'];

        $this->assertSame(404, $component->statusCode);
        $this->assertSame('Not Found', $component->title);
        $this->assertSame('The page does not exist.', $component->message);
        $this->assertNull($component->details);
        $this->assertNull($component->backUrl);
        $this->assertSame('Back to homepage', $component->backLabel);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $component->symbol);
        $this->assertStringStartsWith('data:image/png;base64,', $component->logo);
        $this->assertSame('en', $component->locale);
        $this->assertSame('Enabel', $component->appName);
        $this->assertSame('vendor/@enabel/enabel-bootstrap-theme/dist/css/error.min.css', $component->stylesheet);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new ErrorPage();
        $data = $component->preMount([
            'statusCode' => 500,
            'title' => 'Server Error',
            'message' => 'Something went wrong.',
            'details' => 'Trace ID: abc-123',
            'backUrl' => '/',
            'backLabel' => 'Return home',
            'symbol' => '/custom/symbol.svg',
            'logo' => '/custom/logo.svg',
            'locale' => 'fr',
            'appName' => 'Impala',
            'stylesheet' => 'css/custom-error.css',
        ]);

        $this->assertSame(500, $data['statusCode']);
        $this->assertSame('Server Error', $data['title']);
        $this->assertSame('Something went wrong.', $data['message']);
        $this->assertSame('Trace ID: abc-123', $data['details']);
        $this->assertSame('/', $data['backUrl']);
        $this->assertSame('Return home', $data['backLabel']);
        $this->assertSame('/custom/symbol.svg', $data['symbol']);
        $this->assertSame('/custom/logo.svg', $data['logo']);
        $this->assertSame('fr', $data['locale']);
        $this->assertSame('Impala', $data['appName']);
        $this->assertSame('css/custom-error.css', $data['stylesheet']);
    }

    public function testMissingStatusCodeThrows(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = new ErrorPage();
        $component->preMount(['title' => 'Not Found', 'message' => 'Missing']);
    }

    public function testMissingTitleThrows(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = new ErrorPage();
        $component->preMount(['statusCode' => 404, 'message' => 'Missing']);
    }

    public function testMissingMessageThrows(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = new ErrorPage();
        $component->preMount(['statusCode' => 404, 'title' => 'Not Found']);
    }

    public function testInvalidStatusCodeTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ErrorPage();
        $component->preMount([
            'statusCode' => '404',
            'title' => 'Not Found',
            'message' => 'Missing',
        ]);
    }

    public function testInvalidTitleTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ErrorPage();
        $component->preMount([
            'statusCode' => 404,
            'title' => 404,
            'message' => 'Missing',
        ]);
    }

    public function testInvalidDetailsTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ErrorPage();
        $component->preMount([
            'statusCode' => 404,
            'title' => 'Not Found',
            'message' => 'Missing',
            'details' => 123,
        ]);
    }

    public function testInvalidBackUrlTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ErrorPage();
        $component->preMount([
            'statusCode' => 404,
            'title' => 'Not Found',
            'message' => 'Missing',
            'backUrl' => true,
        ]);
    }

    public function testCustomSymbolAndLogoPathsOverrideDataUriDefaults(): void
    {
        $component = new ErrorPage();
        $data = $component->preMount([
            'statusCode' => 404,
            'title' => 'Not Found',
            'message' => 'Missing',
            'symbol' => '/images/custom-symbol.png',
            'logo' => '/images/custom-logo.png',
        ]);

        $this->assertSame('/images/custom-symbol.png', $data['symbol']);
        $this->assertSame('/images/custom-logo.png', $data['logo']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new ErrorPage();
        $data = $component->preMount([
            'statusCode' => 404,
            'title' => 'Not Found',
            'message' => 'Missing',
            'custom_attribute' => 'value',
        ]);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
