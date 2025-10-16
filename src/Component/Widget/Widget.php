<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Widget;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class Widget
{
    public string $label;
    public string $variant;
    public ?string $icon;
    public ?string $link;
    public ?string $count;

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
            'label' => '',
            'variant' => 'primary',
            'icon' => null,
            'link' => null,
            'count' => null,
        ]);

        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('variant', 'string');
        $resolver->setAllowedTypes('icon', ['string', 'null']);
        $resolver->setAllowedTypes('link', ['string', 'null']);
        $resolver->setAllowedTypes('count', ['string', 'null']);

        $resolver->setAllowedValues('variant', ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']);
    }
}
