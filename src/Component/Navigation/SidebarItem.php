<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Navigation;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class SidebarItem
{
    public string $label;
    public string $link;
    public ?string $icon;
    public ?string $activeRoutePrefix;
    public bool $active;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

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

        $resolved = $resolver->resolve($data);

        if (!isset($data['active'])) {
            $resolved['active'] = $this->isCurrentRoute($resolved['activeRoutePrefix']);
        }

        return $resolved + $data;
    }

    private function isCurrentRoute(?string $prefix): bool
    {
        if (null === $prefix || '' === $prefix) {
            return false;
        }

        $route = $this->requestStack->getCurrentRequest()?->attributes->get('_route');

        return \is_string($route) && ($route === $prefix || str_starts_with($route, $prefix.'.'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setDefaults([
            'label' => '',
            'link' => '#',
            'icon' => null,
            'activeRoutePrefix' => null,
            'active' => false,
        ]);

        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('link', 'string');
        $resolver->setAllowedTypes('icon', ['string', 'null']);
        $resolver->setAllowedTypes('activeRoutePrefix', ['string', 'null']);
        $resolver->setAllowedTypes('active', 'bool');
    }
}
