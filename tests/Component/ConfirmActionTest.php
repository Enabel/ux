<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component;

use Enabel\Ux\Component\ConfirmAction;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ConfirmActionTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function required(): array
    {
        return ['url' => '/admin/users/1/delete', 'tokenId' => 'delete-user-1', 'label' => 'Delete', 'message' => 'Really?'];
    }

    public function testDefaults(): void
    {
        $data = (new ConfirmAction())->preMount($this->required());

        $this->assertSame('Delete', $data['title']);
        $this->assertSame('Delete', $data['confirmLabel']);
        $this->assertSame('danger', $data['variant']);
        $this->assertSame('sm', $data['size']);
        $this->assertNull($data['icon']);
    }

    public function testExplicitTitleAndConfirmLabelAreKept(): void
    {
        $data = (new ConfirmAction())->preMount($this->required() + ['title' => 'Sure?', 'confirmLabel' => 'Yes, delete']);

        $this->assertSame('Sure?', $data['title']);
        $this->assertSame('Yes, delete', $data['confirmLabel']);
    }

    public function testUnknownVariantIsRefused(): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new ConfirmAction())->preMount(['variant' => 'link'] + $this->required());
    }

    public function testUnknownSizeIsRefused(): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new ConfirmAction())->preMount(['size' => 'xl'] + $this->required());
    }

    public function testEachInstanceGetsItsOwnModalId(): void
    {
        $first = (new ConfirmAction())->preMount($this->required());
        $second = (new ConfirmAction())->preMount($this->required());

        $this->assertMatchesRegularExpression('/^confirm-action-[0-9a-f]{12}$/', $first['modalId']);
        $this->assertMatchesRegularExpression('/^confirm-action-[0-9a-f]{12}$/', $second['modalId']);
        $this->assertNotSame($first['modalId'], $second['modalId']);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('requiredOptions')]
    public function testUrlAndTokenIdAreRequired(string $missing): void
    {
        $data = $this->required();
        unset($data[$missing]);

        $this->expectException(MissingOptionsException::class);

        (new ConfirmAction())->preMount($data);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function requiredOptions(): iterable
    {
        yield 'url' => ['url'];
        yield 'tokenId' => ['tokenId'];
        yield 'label' => ['label'];
        yield 'message' => ['message'];
    }
}
