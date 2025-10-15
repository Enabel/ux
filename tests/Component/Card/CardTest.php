<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Card;

use Enabel\Ux\Component\Card\Card;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CardTest extends TestCase
{
    public function testComponentCanBeInstantiatedWithDefaultParameters(): void
    {
        $component = new Card();
        $data = $component->preMount([]);

        $component->title = $data['title'];
        $component->subtitle = $data['subtitle'];
        $component->text = $data['text'];
        $component->image = $data['image'];
        $component->imageAlt = $data['imageAlt'];
        $component->imagePosition = $data['imagePosition'];
        $component->header = $data['header'];
        $component->footer = $data['footer'];
        $component->link = $data['link'];
        $component->linkText = $data['linkText'];

        $this->assertInstanceOf(Card::class, $component);
        $this->assertNull($component->title);
        $this->assertNull($component->subtitle);
        $this->assertNull($component->text);
        $this->assertNull($component->image);
        $this->assertEquals('', $component->imageAlt);
        $this->assertEquals('top', $component->imagePosition);
        $this->assertNull($component->header);
        $this->assertNull($component->footer);
        $this->assertNull($component->link);
        $this->assertEquals('Go somewhere', $component->linkText);
    }

    public function testComponentCanBeInstantiatedWithBasicCard(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'title' => 'Card Title',
            'text' => 'Card text content',
        ]);

        $component->title = $data['title'];
        $component->text = $data['text'];

        $this->assertInstanceOf(Card::class, $component);
        $this->assertEquals('Card Title', $component->title);
        $this->assertEquals('Card text content', $component->text);
    }

    public function testComponentCanBeInstantiatedWithImage(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'image' => '/images/test.jpg',
            'imageAlt' => 'Test image',
            'title' => 'Card with Image',
        ]);

        $component->image = $data['image'];
        $component->imageAlt = $data['imageAlt'];
        $component->title = $data['title'];

        $this->assertEquals('/images/test.jpg', $component->image);
        $this->assertEquals('Test image', $component->imageAlt);
        $this->assertEquals('Card with Image', $component->title);
    }

    public function testComponentSupportsImagePositions(): void
    {
        $positions = ['top', 'bottom'];

        foreach ($positions as $position) {
            $component = new Card();
            $data = $component->preMount([
                'image' => '/images/test.jpg',
                'imagePosition' => $position,
            ]);

            $component->imagePosition = $data['imagePosition'];

            $this->assertEquals($position, $component->imagePosition);
        }
    }

    public function testComponentThrowsExceptionForInvalidImagePosition(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Card();
        $component->preMount(['imagePosition' => 'left']);
    }

    public function testComponentCanBeInstantiatedWithHeaderAndFooter(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'header' => 'Card Header',
            'title' => 'Card Title',
            'footer' => 'Card Footer',
        ]);

        $component->header = $data['header'];
        $component->title = $data['title'];
        $component->footer = $data['footer'];

        $this->assertEquals('Card Header', $component->header);
        $this->assertEquals('Card Title', $component->title);
        $this->assertEquals('Card Footer', $component->footer);
    }

    public function testComponentCanBeInstantiatedWithSubtitle(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'title' => 'Main Title',
            'subtitle' => 'Subtitle Text',
        ]);

        $component->title = $data['title'];
        $component->subtitle = $data['subtitle'];

        $this->assertEquals('Main Title', $component->title);
        $this->assertEquals('Subtitle Text', $component->subtitle);
    }

    public function testComponentCanBeInstantiatedWithLink(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'title' => 'Card Title',
            'link' => '/details',
            'linkText' => 'Learn More',
        ]);

        $component->title = $data['title'];
        $component->link = $data['link'];
        $component->linkText = $data['linkText'];

        $this->assertEquals('Card Title', $component->title);
        $this->assertEquals('/details', $component->link);
        $this->assertEquals('Learn More', $component->linkText);
    }

    public function testComponentCanHandleCompleteCard(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'image' => '/images/product.jpg',
            'imageAlt' => 'Product image',
            'imagePosition' => 'top',
            'header' => 'Featured',
            'title' => 'Product Name',
            'subtitle' => 'Premium Quality',
            'text' => 'Product description goes here.',
            'link' => '/product/details',
            'linkText' => 'View Details',
            'footer' => 'Last updated today',
        ]);

        $component->image = $data['image'];
        $component->imageAlt = $data['imageAlt'];
        $component->imagePosition = $data['imagePosition'];
        $component->header = $data['header'];
        $component->title = $data['title'];
        $component->subtitle = $data['subtitle'];
        $component->text = $data['text'];
        $component->link = $data['link'];
        $component->linkText = $data['linkText'];
        $component->footer = $data['footer'];

        $this->assertEquals('/images/product.jpg', $component->image);
        $this->assertEquals('Product image', $component->imageAlt);
        $this->assertEquals('top', $component->imagePosition);
        $this->assertEquals('Featured', $component->header);
        $this->assertEquals('Product Name', $component->title);
        $this->assertEquals('Premium Quality', $component->subtitle);
        $this->assertEquals('Product description goes here.', $component->text);
        $this->assertEquals('/product/details', $component->link);
        $this->assertEquals('View Details', $component->linkText);
        $this->assertEquals('Last updated today', $component->footer);
    }

    public function testComponentThrowsExceptionForInvalidTitleType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Card();
        $component->preMount(['title' => 123]);
    }

    public function testComponentThrowsExceptionForInvalidImageType(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $component = new Card();
        $component->preMount(['image' => 123]);
    }

    public function testPreMountReturnsArrayWithResolvedData(): void
    {
        $component = new Card();
        $result = $component->preMount([
            'title' => 'Test Card',
            'text' => 'Test content',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('subtitle', $result);
        $this->assertArrayHasKey('text', $result);
        $this->assertArrayHasKey('image', $result);
        $this->assertArrayHasKey('imageAlt', $result);
        $this->assertArrayHasKey('imagePosition', $result);
        $this->assertArrayHasKey('header', $result);
        $this->assertArrayHasKey('footer', $result);
        $this->assertArrayHasKey('link', $result);
        $this->assertArrayHasKey('linkText', $result);
        $this->assertEquals('Test Card', $result['title']);
        $this->assertEquals('Test content', $result['text']);
    }

    public function testPreMountPreservesAdditionalData(): void
    {
        $component = new Card();
        $result = $component->preMount([
            'title' => 'Test',
            'custom_attribute' => 'value',
        ]);

        $this->assertArrayHasKey('custom_attribute', $result);
        $this->assertEquals('value', $result['custom_attribute']);
    }

    public function testComponentCanHandleImageAtBottom(): void
    {
        $component = new Card();
        $data = $component->preMount([
            'title' => 'Card Title',
            'image' => '/images/test.jpg',
            'imagePosition' => 'bottom',
        ]);

        $component->title = $data['title'];
        $component->image = $data['image'];
        $component->imagePosition = $data['imagePosition'];

        $this->assertEquals('Card Title', $component->title);
        $this->assertEquals('/images/test.jpg', $component->image);
        $this->assertEquals('bottom', $component->imagePosition);
    }
}
