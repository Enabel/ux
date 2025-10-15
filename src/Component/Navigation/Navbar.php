<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Navigation;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class Navbar
{
    public ?string $logo;
    public ?string $name;
    public ?string $link;
    public string $theme;
    public string $expand;
    public bool $fixed;
    public string $fixedPosition;

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
            'logo' => null,
            'name' => null,
            'link' => '/',
            'theme' => 'light',
            'expand' => 'lg',
            'fixed' => false,
            'fixedPosition' => 'top',
        ]);

        $resolver->setAllowedTypes('logo', ['string', 'null']);
        $resolver->setAllowedTypes('name', ['string', 'null']);
        $resolver->setAllowedTypes('link', ['string', 'null']);
        $resolver->setAllowedTypes('theme', 'string');
        $resolver->setAllowedTypes('expand', 'string');
        $resolver->setAllowedTypes('fixed', 'bool');
        $resolver->setAllowedTypes('fixedPosition', 'string');

        $resolver->setAllowedValues('theme', ['light', 'dark']);
        $resolver->setAllowedValues('expand', ['sm', 'md', 'lg', 'xl', 'xxl', 'always', 'never']);
        $resolver->setAllowedValues('fixedPosition', ['top', 'bottom']);
    }
}
