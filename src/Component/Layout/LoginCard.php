<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Layout;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class LoginCard
{
    public ?string $logo;
    public ?string $title;
    public string $background;
    public ?string $backgroundImage;
    public string $size;

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
            'title' => null,
            'background' => 'primary',
            'backgroundImage' => null,
            'size' => 'md',
        ]);

        $resolver->setAllowedTypes('logo', ['string', 'null']);
        $resolver->setAllowedTypes('title', ['string', 'null']);
        $resolver->setAllowedTypes('background', 'string');
        $resolver->setAllowedTypes('backgroundImage', ['string', 'null']);
        $resolver->setAllowedTypes('size', 'string');

        $resolver->setAllowedValues('background', ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']);
        $resolver->setAllowedValues('size', ['sm', 'md', 'lg', 'xl']);
    }
}
