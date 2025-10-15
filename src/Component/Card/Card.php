<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Card;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class Card
{
    public ?string $title;
    public ?string $subtitle;
    public ?string $text;
    public ?string $image;
    public ?string $imageAlt;
    public string $imagePosition;
    public ?string $header;
    public ?string $footer;
    public ?string $link;
    public ?string $linkText;

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        return $resolver->resolve($data) + $data;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setDefaults([
            'title' => null,
            'subtitle' => null,
            'text' => null,
            'image' => null,
            'imageAlt' => '',
            'imagePosition' => 'top',
            'header' => null,
            'footer' => null,
            'link' => null,
            'linkText' => 'Go somewhere',
        ]);

        $resolver->setAllowedTypes('title', ['string', 'null']);
        $resolver->setAllowedTypes('subtitle', ['string', 'null']);
        $resolver->setAllowedTypes('text', ['string', 'null']);
        $resolver->setAllowedTypes('image', ['string', 'null']);
        $resolver->setAllowedTypes('imageAlt', ['string', 'null']);
        $resolver->setAllowedTypes('imagePosition', 'string');
        $resolver->setAllowedTypes('header', ['string', 'null']);
        $resolver->setAllowedTypes('footer', ['string', 'null']);
        $resolver->setAllowedTypes('link', ['string', 'null']);
        $resolver->setAllowedTypes('linkText', ['string', 'null']);

        $resolver->setAllowedValues('imagePosition', ['top', 'bottom']);
    }
}
