<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\ImpersonateDropdown;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ImpersonateDropdownTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithRequiredParameters(): void
    {
        $component = new ImpersonateDropdown();
        $data = $component->preMount(['searchUrl' => '/admin/users/search']);

        $component->searchUrl = $data['searchUrl'];
        $component->searchPlaceholder = $data['searchPlaceholder'];
        $component->noResultsLabel = $data['noResultsLabel'];
        $component->icon = $data['icon'];
        $component->title = $data['title'];
        $component->exitParameter = $data['exitParameter'];
        $component->debounce = $data['debounce'];
        $component->minLength = $data['minLength'];

        $this->assertSame('/admin/users/search', $component->searchUrl);
        $this->assertSame('', $component->searchPlaceholder);
        $this->assertSame('No results', $component->noResultsLabel);
        $this->assertSame('fa6-solid:user-secret', $component->icon);
        $this->assertNull($component->title);
        $this->assertSame('_switch_user', $component->exitParameter);
        $this->assertSame(250, $component->debounce);
        $this->assertSame(2, $component->minLength);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = new ImpersonateDropdown();
        $data = $component->preMount([
            'searchUrl' => '/api/search',
            'searchPlaceholder' => 'Rechercher…',
            'noResultsLabel' => 'Aucun résultat',
            'icon' => 'fa6-solid:mask',
            'title' => 'Incarner un utilisateur',
            'exitParameter' => '_switch',
            'debounce' => 500,
            'minLength' => 3,
        ]);

        $this->assertSame('/api/search', $data['searchUrl']);
        $this->assertSame('Rechercher…', $data['searchPlaceholder']);
        $this->assertSame('Aucun résultat', $data['noResultsLabel']);
        $this->assertSame('fa6-solid:mask', $data['icon']);
        $this->assertSame('Incarner un utilisateur', $data['title']);
        $this->assertSame('_switch', $data['exitParameter']);
        $this->assertSame(500, $data['debounce']);
        $this->assertSame(3, $data['minLength']);
    }

    public function testMissingSearchUrlThrows(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount([]);
    }

    public function testInvalidSearchUrlTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount(['searchUrl' => null]);
    }

    public function testInvalidSearchPlaceholderTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount([
            'searchUrl' => '/search',
            'searchPlaceholder' => 123,
        ]);
    }

    public function testInvalidTitleTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount([
            'searchUrl' => '/search',
            'title' => true,
        ]);
    }

    public function testInvalidDebounceTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount([
            'searchUrl' => '/search',
            'debounce' => '250',
        ]);
    }

    public function testInvalidExitParameterTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount([
            'searchUrl' => '/search',
            'exitParameter' => 42,
        ]);
    }

    public function testInvalidMinLengthTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new ImpersonateDropdown();
        $component->preMount([
            'searchUrl' => '/search',
            'minLength' => '2',
        ]);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new ImpersonateDropdown();
        $data = $component->preMount([
            'searchUrl' => '/search',
            'custom_attribute' => 'value',
        ]);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
