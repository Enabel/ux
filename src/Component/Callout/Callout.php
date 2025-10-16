<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Callout;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class Callout
{
    public ?string $title;
    public ?string $message;
    public string $type;
    public ?string $icon;
    public ?string $size;

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
            'message' => null,
            'type' => 'info',
            'icon' => null,
            'size' => null,
        ]);

        $resolver->setAllowedTypes('title', ['string', 'null']);
        $resolver->setAllowedTypes('message', ['string', 'null']);
        $resolver->setAllowedTypes('type', 'string');
        $resolver->setAllowedTypes('icon', ['string', 'null']);
        $resolver->setAllowedTypes('size', ['string', 'null']);

        $resolver->setAllowedValues('type', ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']);
        $resolver->setAllowedValues('size', [null, 'sm']);
    }
}