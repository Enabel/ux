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

class MenuItem
{
    public string $label;
    public ?string $link;
    public bool $active;
    public bool $disabled;
    public ?string $icon;
    public bool $isDropdown;
    /**
     * @var array<string, string>
     */
    public array $dropdownItems;

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

        // Auto-detect active status if not explicitly set and link is provided
        if (!isset($data['active']) && isset($resolved['link']) && $resolved['link']) {
            $resolved['active'] = $this->isCurrentRoute($resolved['link']);
        }

        // Auto-detect active status for dropdown items
        if ($resolved['isDropdown'] && !empty($resolved['dropdownItems'])) {
            $resolved['dropdownItems'] = $this->processDropdownItems($resolved['dropdownItems']);
        }

        return $resolved + $data;
    }

    private function isCurrentRoute(string $href): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $currentPath = $request->getPathInfo();

        // Exact match
        if ($currentPath === $href) {
            return true;
        }

        // Check if current path starts with href (for parent routes)
        // But only if href is not just '/'
        if ('/' !== $href && str_starts_with($currentPath, $href)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<array<string, mixed>> $items
     *
     * @return array<array<string, mixed>>
     */
    private function processDropdownItems(array $items): array
    {
        foreach ($items as $key => $item) {
            // Skip dividers and headers
            if (isset($item['divider']) || isset($item['header'])) {
                continue;
            }

            // Auto-detect active status for dropdown items if not explicitly set
            if (!isset($item['active']) && isset($item['link']) && $item['link']) {
                $items[$key]['active'] = $this->isCurrentRoute($item['link']);
            }
        }

        return $items;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setDefaults([
            'label' => '',
            'link' => null,
            'active' => false,
            'disabled' => false,
            'icon' => null,
            'isDropdown' => false,
            'dropdownItems' => [],
        ]);

        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('link', ['string', 'null']);
        $resolver->setAllowedTypes('active', 'bool');
        $resolver->setAllowedTypes('disabled', 'bool');
        $resolver->setAllowedTypes('icon', ['string', 'null']);
        $resolver->setAllowedTypes('isDropdown', 'bool');
        $resolver->setAllowedTypes('dropdownItems', 'array');
    }
}
