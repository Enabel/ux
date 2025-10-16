<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Timeline;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class Timeline
{
    public ?string $title;
    /** @var array<int, array<string, mixed>> */
    public array $items;
    public bool $showColors;

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
            'title' => null,
            'items' => [],
            'showColors' => false,
        ]);

        $resolver->setAllowedTypes('title', ['string', 'null']);
        $resolver->setAllowedTypes('items', 'array');
        $resolver->setAllowedTypes('showColors', 'bool');

        // Validate items structure
        $resolver->setNormalizer('items', function ($resolver, $items) {
            if (empty($items)) {
                return [];
            }

            $validatedItems = [];
            foreach ($items as $item) {
                if (!\is_array($item)) {
                    throw new \InvalidArgumentException('Each item must be an array.');
                }

                // Normalize each item
                $itemResolver = new OptionsResolver();
                $itemResolver->setDefaults([
                    'date' => null,
                    'label' => null,
                    'description' => null,
                    'icon' => null,
                    'color' => null,
                ]);

                $itemResolver->setAllowedTypes('date', ['string', 'null']);
                $itemResolver->setAllowedTypes('label', ['string', 'null']);
                $itemResolver->setAllowedTypes('description', ['string', 'null']);
                $itemResolver->setAllowedTypes('icon', ['string', 'null']);
                $itemResolver->setAllowedTypes('color', ['string', 'null']);

                // Validate color against Bootstrap theme colors
                $itemResolver->setAllowedValues('color', [
                    null, 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark',
                ]);

                $validatedItems[] = $itemResolver->resolve($item);
            }

            return $validatedItems;
        });
    }
}
