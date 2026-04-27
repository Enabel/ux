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

class UserMenu
{
    /**
     * Predefined palette used to derive a stable background color from the user name
     * when no explicit `initialsColor` is provided.
     */
    private const COLOR_PALETTE = [
        '#0ea5e9',
        '#10b981',
        '#f59e0b',
        '#ef4444',
        '#8b5cf6',
        '#ec4899',
        '#14b8a6',
        '#f97316',
        '#6366f1',
        '#84cc16',
    ];

    public string $name;
    public ?string $image;
    public string $initials;
    public string $initialsColor;
    public string $dropdownAlign;
    public bool $hideName;
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $items;

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

        // Auto-derive initials from name if not explicitly provided
        if (null === $resolved['initials']) {
            $resolved['initials'] = $this->deriveInitials($resolved['name']);
        }

        // Auto-derive a stable background color from the name if not explicitly provided
        if (null === $resolved['initialsColor']) {
            $resolved['initialsColor'] = $this->deriveColor($resolved['name']);
        }

        // Filter items by visibility and auto-detect active state
        $resolved['items'] = $this->processItems($resolved['items']);

        return $resolved + $data;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setRequired('name');
        $resolver->setDefaults([
            'image' => null,
            'initials' => null,
            'initialsColor' => null,
            'dropdownAlign' => 'end',
            'hideName' => true,
            'items' => [],
        ]);

        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('image', ['string', 'null']);
        $resolver->setAllowedTypes('initials', ['string', 'null']);
        $resolver->setAllowedTypes('initialsColor', ['string', 'null']);
        $resolver->setAllowedTypes('dropdownAlign', 'string');
        $resolver->setAllowedTypes('hideName', 'bool');
        $resolver->setAllowedTypes('items', 'array');

        $resolver->setAllowedValues('dropdownAlign', ['start', 'end']);
    }

    private function deriveInitials(string $name): string
    {
        $name = trim($name);
        if ('' === $name) {
            return '';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];

        if (1 === \count($parts)) {
            return mb_strtoupper(mb_substr($parts[0], 0, 2));
        }

        $first = mb_strtoupper(mb_substr($parts[0], 0, 1));
        $last = mb_strtoupper(mb_substr((string) end($parts), 0, 1));

        return $first.$last;
    }

    private function deriveColor(string $name): string
    {
        if ('' === trim($name)) {
            return self::COLOR_PALETTE[0];
        }

        $hash = crc32($name);

        return self::COLOR_PALETTE[$hash % \count(self::COLOR_PALETTE)];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<int, array<string, mixed>>
     */
    private function processItems(array $items): array
    {
        $processed = [];
        foreach ($items as $item) {
            // Filter out items explicitly marked as not visible
            if (isset($item['visible']) && false === $item['visible']) {
                continue;
            }

            // Skip dividers and headers (no active state, no link processing)
            if (isset($item['divider']) || isset($item['header'])) {
                $processed[] = $item;
                continue;
            }

            // Auto-detect active state if not explicitly set and link is provided
            if (!isset($item['active']) && isset($item['link']) && $item['link']) {
                $item['active'] = $this->isCurrentRoute((string) $item['link']);
            }

            $processed[] = $item;
        }

        return $processed;
    }

    private function isCurrentRoute(string $href): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $currentPath = $request->getPathInfo();

        if ($currentPath === $href) {
            return true;
        }

        if ('/' !== $href && str_starts_with($currentPath, $href)) {
            return true;
        }

        return false;
    }
}
