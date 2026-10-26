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

class AdminLayout
{
    public string $title;

    /** @var list<array{label: string, link?: string|null}> */
    public array $breadcrumb = [];

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
        $resolved['breadcrumb'] = array_map(static function (array $item): array {
            if (null === ($item['link'] ?? null) || '' === $item['link']) {
                unset($item['link']);
            }

            return $item;
        }, $resolved['breadcrumb']);

        return $resolved + $data;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setRequired('title');
        $resolver->setDefaults(['breadcrumb' => []]);

        $resolver->setAllowedTypes('title', 'string');
        $resolver->setAllowedTypes('breadcrumb', 'array');
        $resolver->setAllowedValues('breadcrumb', static function (array $breadcrumb): bool {
            if (!array_is_list($breadcrumb)) {
                return false;
            }

            foreach ($breadcrumb as $item) {
                if (!\is_array($item) || !\is_string($item['label'] ?? null) || (null !== ($item['link'] ?? null) && !\is_string($item['link']))) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Maps a flash type to a type accepted by the Alert component.
     */
    public function alertType(string $flashType): string
    {
        return match ($flashType) {
            'error' => 'danger',
            'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark' => $flashType,
            default => 'info',
        };
    }
}
