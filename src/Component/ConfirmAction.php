<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

/**
 * A button opening a modal whose form sends a plain POST protected by a CSRF token.
 */
class ConfirmAction
{
    public string $url;
    public string $tokenId;
    public string $label;
    public string $message;
    public ?string $title = null;
    public ?string $confirmLabel = null;
    public string $variant = 'danger';
    public ?string $icon = null;
    public string $size = 'sm';
    public string $modalId;

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

        $resolved = $resolver->resolve($data) + $data;
        $resolved['title'] ??= $resolved['label'];
        $resolved['confirmLabel'] ??= $resolved['label'];
        $resolved['modalId'] = 'confirm-action-'.bin2hex(random_bytes(6));

        return $resolved;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setRequired(['url', 'tokenId', 'label', 'message']);
        $resolver->setDefaults([
            'title' => null,
            'confirmLabel' => null,
            'variant' => 'danger',
            'icon' => null,
            'size' => 'sm',
        ]);

        $resolver->setAllowedTypes('url', 'string');
        $resolver->setAllowedTypes('tokenId', 'string');
        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('message', 'string');
        $resolver->setAllowedTypes('title', ['string', 'null']);
        $resolver->setAllowedTypes('confirmLabel', ['string', 'null']);
        $resolver->setAllowedTypes('variant', 'string');
        $resolver->setAllowedTypes('icon', ['string', 'null']);
        $resolver->setAllowedTypes('size', 'string');

        $resolver->setAllowedValues('variant', ['danger', 'primary', 'success', 'warning', 'secondary']);
        $resolver->setAllowedValues('size', ['sm', 'md']);
    }
}
