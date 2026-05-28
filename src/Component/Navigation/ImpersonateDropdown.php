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

class ImpersonateDropdown
{
    public string $searchUrl;
    public string $searchPlaceholder;
    public string $noResultsLabel;
    public string $icon;
    public ?string $title;
    public string $exitParameter;
    public int $debounce;

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
        $resolver->setRequired('searchUrl');
        $resolver->setDefaults([
            'searchPlaceholder' => '',
            'noResultsLabel' => 'No results',
            'icon' => 'fa6-solid:user-secret',
            'title' => null,
            'exitParameter' => '_switch_user',
            'debounce' => 250,
        ]);

        $resolver->setAllowedTypes('searchUrl', 'string');
        $resolver->setAllowedTypes('searchPlaceholder', 'string');
        $resolver->setAllowedTypes('noResultsLabel', 'string');
        $resolver->setAllowedTypes('icon', 'string');
        $resolver->setAllowedTypes('title', ['string', 'null']);
        $resolver->setAllowedTypes('exitParameter', 'string');
        $resolver->setAllowedTypes('debounce', 'int');
    }
}
