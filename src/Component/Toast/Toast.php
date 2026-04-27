<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Toast;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class Toast
{
    public ?string $text;
    public string $type;
    public bool $dismissible;
    public bool $autohide;
    public int $delay;
    public string $politeness;

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
            'text' => null,
            'type' => 'info',
            'dismissible' => true,
            'autohide' => true,
            'delay' => 5000,
            'politeness' => 'polite',
        ]);

        $resolver->setAllowedTypes('text', ['string', 'null']);
        $resolver->setAllowedTypes('type', 'string');
        $resolver->setAllowedTypes('dismissible', 'bool');
        $resolver->setAllowedTypes('autohide', 'bool');
        $resolver->setAllowedTypes('delay', 'int');
        $resolver->setAllowedTypes('politeness', 'string');

        $resolver->setAllowedValues('type', ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']);
        $resolver->setAllowedValues('politeness', ['polite', 'assertive']);
    }
}
