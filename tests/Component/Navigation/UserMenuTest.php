<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Navigation;

use Enabel\Ux\Component\Navigation\UserMenu;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class UserMenuTest extends TestCase
{
    private function createUserMenu(?Request $request = null): UserMenu
    {
        $requestStack = new RequestStack();
        if ($request) {
            $requestStack->push($request);
        }

        return new UserMenu($requestStack);
    }

    public function testNameIsRequired(): void
    {
        $this->expectException(MissingOptionsException::class);

        $component = $this->createUserMenu();
        $component->preMount([]);
    }

    public function testDefaultsAreApplied(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'Damien Lagae']);

        $this->assertSame('Damien Lagae', $data['name']);
        $this->assertNull($data['image']);
        $this->assertSame('end', $data['dropdownAlign']);
        $this->assertTrue($data['hideName']);
        $this->assertSame([], $data['items']);
    }

    public function testInitialsAreDerivedFromTwoWordName(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'Damien Lagae']);

        $this->assertSame('DL', $data['initials']);
    }

    public function testInitialsUseFirstAndLastWordOnly(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'John Ronald Reuel Tolkien']);

        $this->assertSame('JT', $data['initials']);
    }

    public function testInitialsAreDerivedFromSingleWordName(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'Damien']);

        $this->assertSame('DA', $data['initials']);
    }

    public function testInitialsAreUppercaseAndMultibyteSafe(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'éric Dübois']);

        $this->assertSame('ÉD', $data['initials']);
    }

    public function testEmptyNameProducesEmptyInitials(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => '   ']);

        $this->assertSame('', $data['initials']);
    }

    public function testInitialsCanBeOverridden(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'initials' => 'XY',
        ]);

        $this->assertSame('XY', $data['initials']);
    }

    public function testInitialsColorIsDerivedFromName(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'Damien Lagae']);

        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $data['initialsColor']);
    }

    public function testInitialsColorIsStableForSameName(): void
    {
        $component = $this->createUserMenu();
        $first = $component->preMount(['name' => 'Damien Lagae']);
        $second = $component->preMount(['name' => 'Damien Lagae']);

        $this->assertSame($first['initialsColor'], $second['initialsColor']);
    }

    public function testInitialsColorCanBeOverridden(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'initialsColor' => '#123456',
        ]);

        $this->assertSame('#123456', $data['initialsColor']);
    }

    public function testImageIsAccepted(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'image' => 'data:image/png;base64,AAAA',
        ]);

        $this->assertSame('data:image/png;base64,AAAA', $data['image']);
    }

    public function testItemsAreKeptByDefault(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['label' => 'Profile', 'link' => '/profile'],
                ['label' => 'Logout', 'link' => '/logout'],
            ],
        ]);

        $this->assertCount(2, $data['items']);
    }

    public function testItemsWithVisibleFalseAreFiltered(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['label' => 'Profile', 'link' => '/profile'],
                ['label' => 'Admin', 'link' => '/admin', 'visible' => false],
                ['label' => 'Logout', 'link' => '/logout'],
            ],
        ]);

        $this->assertCount(2, $data['items']);
        $this->assertSame('Profile', $data['items'][0]['label']);
        $this->assertSame('Logout', $data['items'][1]['label']);
    }

    public function testItemsWithVisibleTrueAreKept(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['label' => 'Profile', 'link' => '/profile', 'visible' => true],
            ],
        ]);

        $this->assertCount(1, $data['items']);
    }

    public function testDividersAndHeadersArePreserved(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['header' => true, 'label' => 'Section'],
                ['label' => 'Profile', 'link' => '/profile'],
                ['divider' => true],
                ['label' => 'Logout', 'link' => '/logout'],
            ],
        ]);

        $this->assertCount(4, $data['items']);
        $this->assertTrue($data['items'][0]['header']);
        $this->assertTrue($data['items'][2]['divider']);
    }

    public function testItemsAutoDetectActiveState(): void
    {
        $request = Request::create('/profile');
        $component = $this->createUserMenu($request);
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['label' => 'Profile', 'link' => '/profile'],
                ['label' => 'Logout', 'link' => '/logout'],
            ],
        ]);

        $this->assertTrue($data['items'][0]['active']);
        $this->assertFalse($data['items'][1]['active']);
    }

    public function testItemsAutoDetectActiveStateWithPrefix(): void
    {
        $request = Request::create('/admin/users/123');
        $component = $this->createUserMenu($request);
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['label' => 'Admin', 'link' => '/admin'],
            ],
        ]);

        $this->assertTrue($data['items'][0]['active']);
    }

    public function testItemsManualActiveOverridesAutoDetection(): void
    {
        $request = Request::create('/profile');
        $component = $this->createUserMenu($request);
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'items' => [
                ['label' => 'Profile', 'link' => '/profile', 'active' => false],
            ],
        ]);

        $this->assertFalse($data['items'][0]['active']);
    }

    public function testHideNameCanBeDisabled(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'hideName' => false,
        ]);

        $this->assertFalse($data['hideName']);
    }

    public function testDropdownAlignDefaultsToEnd(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount(['name' => 'Damien Lagae']);

        $this->assertSame('end', $data['dropdownAlign']);
    }

    public function testDropdownAlignCanBeStart(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'dropdownAlign' => 'start',
        ]);

        $this->assertSame('start', $data['dropdownAlign']);
    }

    public function testInvalidDropdownAlignThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createUserMenu();
        $component->preMount([
            'name' => 'Damien Lagae',
            'dropdownAlign' => 'middle',
        ]);
    }

    public function testInvalidNameTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createUserMenu();
        $component->preMount(['name' => 123]);
    }

    public function testInvalidImageTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createUserMenu();
        $component->preMount([
            'name' => 'Damien Lagae',
            'image' => 123,
        ]);
    }

    public function testInvalidItemsTypeThrows(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = $this->createUserMenu();
        $component->preMount([
            'name' => 'Damien Lagae',
            'items' => 'not-an-array',
        ]);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = $this->createUserMenu();
        $data = $component->preMount([
            'name' => 'Damien Lagae',
            'custom_attribute' => 'value',
        ]);

        $this->assertArrayHasKey('custom_attribute', $data);
        $this->assertSame('value', $data['custom_attribute']);
    }
}
