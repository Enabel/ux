<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\MenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class MenuItemTest extends TestCase
{
    private function createMenuItem(?Request $request = null): MenuItem
    {
        $requestStack = new RequestStack();
        if ($request) {
            $requestStack->push($request);
        }

        return new MenuItem($requestStack);
    }

    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([]);

        $component->label = $data['label'];
        $component->link = $data['link'];
        $component->active = $data['active'];
        $component->disabled = $data['disabled'];

        $this->assertInstanceOf(MenuItem::class, $component);
        $this->assertEquals('', $component->label);
        $this->assertNull($component->link);
        $this->assertFalse($component->active);
        $this->assertFalse($component->disabled);
    }

    public function testComponentCanBeInstantiatedWithCustomParameters(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
            'active' => true,
            'disabled' => false,
        ]);

        $component->label = $data['label'];
        $component->link = $data['link'];
        $component->active = $data['active'];
        $component->disabled = $data['disabled'];

        $this->assertEquals('Home', $component->label);
        $this->assertEquals('/home', $component->link);
        $this->assertTrue($component->active);
        $this->assertFalse($component->disabled);
    }

    public function testComponentCanBeDisabled(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Disabled Link',
            'link' => '/disabled',
            'disabled' => true,
        ]);

        $component->label = $data['label'];
        $component->link = $data['link'];
        $component->disabled = $data['disabled'];

        $this->assertEquals('Disabled Link', $component->label);
        $this->assertTrue($component->disabled);
    }

    public function testComponentCanBeActive(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Current Page',
            'link' => '/current',
            'active' => true,
        ]);

        $component->label = $data['label'];
        $component->link = $data['link'];
        $component->active = $data['active'];

        $this->assertEquals('Current Page', $component->label);
        $this->assertTrue($component->active);
    }

    public function testComponentCanHaveNoLink(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Text Only',
            'link' => null,
        ]);

        $component->label = $data['label'];
        $component->link = $data['link'];

        $this->assertEquals('Text Only', $component->label);
        $this->assertNull($component->link);
    }

    public function testComponentThrowsExceptionForInvalidLabelType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['label' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidLinkType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['link' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidActiveType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['active' => 'yes']);
    }

    public function testComponentThrowsExceptionForInvalidDisabledType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['disabled' => 'no']);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $component = $this->createMenuItem();
        $result = $component->preMount(['label' => 'About', 'link' => '/about']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('link', $result);
        $this->assertArrayHasKey('active', $result);
        $this->assertArrayHasKey('disabled', $result);
        $this->assertEquals('About', $result['label']);
        $this->assertEquals('/about', $result['link']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = $this->createMenuItem();
        $result = $component->preMount(['label' => 'Test', 'custom_attribute' => 'value']);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }

    public function testComponentCanHaveIcon(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
            'icon' => 'bi:house',
        ]);

        $component->label = $data['label'];
        $component->link = $data['link'];
        $component->icon = $data['icon'];

        $this->assertEquals('Home', $component->label);
        $this->assertEquals('/home', $component->link);
        $this->assertEquals('bi:house', $component->icon);
    }

    public function testComponentCanBeDropdown(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Dropdown',
            'isDropdown' => true,
            'dropdownItems' => [
                ['label' => 'Item 1', 'link' => '/item1'],
                ['label' => 'Item 2', 'link' => '/item2'],
            ],
        ]);

        $component->label = $data['label'];
        $component->isDropdown = $data['isDropdown'];
        $component->dropdownItems = $data['dropdownItems'];

        $this->assertEquals('Dropdown', $component->label);
        $this->assertTrue($component->isDropdown);
        $this->assertCount(2, $component->dropdownItems);
    }

    public function testComponentDropdownCanHaveIconsInItems(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Actions',
            'isDropdown' => true,
            'dropdownItems' => [
                ['label' => 'Edit', 'link' => '/edit', 'icon' => 'bi:pencil'],
                ['label' => 'Delete', 'link' => '/delete', 'icon' => 'bi:trash'],
            ],
        ]);

        $component->dropdownItems = $data['dropdownItems'];

        $this->assertEquals('bi:pencil', $component->dropdownItems[0]['icon']);
        $this->assertEquals('bi:trash', $component->dropdownItems[1]['icon']);
    }

    public function testComponentDropdownCanHaveDividers(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Menu',
            'isDropdown' => true,
            'dropdownItems' => [
                ['label' => 'Item 1', 'link' => '/item1'],
                ['divider' => true],
                ['label' => 'Item 2', 'link' => '/item2'],
            ],
        ]);

        $component->dropdownItems = $data['dropdownItems'];

        $this->assertTrue($component->dropdownItems[1]['divider']);
    }

    public function testComponentDropdownCanHaveHeaders(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Menu',
            'isDropdown' => true,
            'dropdownItems' => [
                ['header' => true, 'label' => 'Section 1'],
                ['label' => 'Item 1', 'link' => '/item1'],
            ],
        ]);

        $component->dropdownItems = $data['dropdownItems'];

        $this->assertTrue($component->dropdownItems[0]['header']);
        $this->assertEquals('Section 1', $component->dropdownItems[0]['label']);
    }

    public function testComponentThrowsExceptionForInvalidIconType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['icon' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidIsDropdownType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['isDropdown' => 'yes']);
    }

    public function testComponentThrowsExceptionForInvalidDropdownItemsType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createMenuItem();
        $component->preMount(['dropdownItems' => 'not-an-array']);
    }

    public function testPreMountIncludesNewProperties(): void
    {
        $component = $this->createMenuItem();
        $result = $component->preMount([
            'label' => 'Test',
            'icon' => 'bi:star',
            'isDropdown' => true,
            'dropdownItems' => [],
        ]);

        $this->assertArrayHasKey('icon', $result);
        $this->assertArrayHasKey('isDropdown', $result);
        $this->assertArrayHasKey('dropdownItems', $result);
        $this->assertEquals('bi:star', $result['icon']);
        $this->assertTrue($result['isDropdown']);
        $this->assertIsArray($result['dropdownItems']);
    }

    public function testAutoDetectActiveWithExactMatch(): void
    {
        $request = Request::create('/home');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
        ]);

        $this->assertTrue($data['active']);
    }

    public function testAutoDetectActiveWithNoMatch(): void
    {
        $request = Request::create('/about');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
        ]);

        $this->assertFalse($data['active']);
    }

    public function testAutoDetectActiveWithPrefixMatch(): void
    {
        $request = Request::create('/products/123');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Products',
            'link' => '/products',
        ]);

        $this->assertTrue($data['active']);
    }

    public function testAutoDetectActiveDoesNotMatchRootForSubPaths(): void
    {
        $request = Request::create('/home');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Root',
            'link' => '/',
        ]);

        $this->assertFalse($data['active']);
    }

    public function testAutoDetectActiveMatchesRootExactly(): void
    {
        $request = Request::create('/');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Root',
            'link' => '/',
        ]);

        $this->assertTrue($data['active']);
    }

    public function testManualActiveOverridesAutoDetection(): void
    {
        $request = Request::create('/about');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
            'active' => true,
        ]);

        $this->assertTrue($data['active']);
    }

    public function testManualInactiveOverridesAutoDetection(): void
    {
        $request = Request::create('/home');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
            'active' => false,
        ]);

        $this->assertFalse($data['active']);
    }

    public function testAutoDetectActiveWorksWithoutRequest(): void
    {
        $component = $this->createMenuItem();
        $data = $component->preMount([
            'label' => 'Home',
            'link' => '/home',
        ]);

        $this->assertFalse($data['active']);
    }

    public function testAutoDetectActiveInDropdownItems(): void
    {
        $request = Request::create('/profile');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Account',
            'isDropdown' => true,
            'dropdownItems' => [
                ['label' => 'Profile', 'link' => '/profile'],
                ['label' => 'Settings', 'link' => '/settings'],
            ],
        ]);

        $this->assertTrue($data['dropdownItems'][0]['active']);
        $this->assertFalse($data['dropdownItems'][1]['active']);
    }

    public function testAutoDetectActiveInDropdownItemsWithPrefixMatch(): void
    {
        $request = Request::create('/settings/privacy');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Account',
            'isDropdown' => true,
            'dropdownItems' => [
                ['label' => 'Profile', 'link' => '/profile'],
                ['label' => 'Settings', 'link' => '/settings'],
            ],
        ]);

        $this->assertFalse($data['dropdownItems'][0]['active']);
        $this->assertTrue($data['dropdownItems'][1]['active']);
    }

    public function testAutoDetectActiveSkipsDividersAndHeaders(): void
    {
        $request = Request::create('/profile');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Menu',
            'isDropdown' => true,
            'dropdownItems' => [
                ['header' => true, 'label' => 'Account'],
                ['label' => 'Profile', 'link' => '/profile'],
                ['divider' => true],
                ['label' => 'Settings', 'link' => '/settings'],
            ],
        ]);

        $this->assertArrayNotHasKey('active', $data['dropdownItems'][0]);
        $this->assertTrue($data['dropdownItems'][1]['active']);
        $this->assertArrayNotHasKey('active', $data['dropdownItems'][2]);
        $this->assertFalse($data['dropdownItems'][3]['active']);
    }

    public function testManualActiveInDropdownItemOverridesAutoDetection(): void
    {
        $request = Request::create('/profile');
        $component = $this->createMenuItem($request);
        $data = $component->preMount([
            'label' => 'Account',
            'isDropdown' => true,
            'dropdownItems' => [
                ['label' => 'Profile', 'link' => '/profile', 'active' => false],
                ['label' => 'Settings', 'link' => '/settings', 'active' => true],
            ],
        ]);

        $this->assertFalse($data['dropdownItems'][0]['active']);
        $this->assertTrue($data['dropdownItems'][1]['active']);
    }
}
