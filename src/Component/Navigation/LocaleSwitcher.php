<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Navigation;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\PreMount;

class LocaleSwitcher
{
    public const string LABEL_NAME = 'name';
    public const string LABEL_CODE = 'code';
    public const string LABEL_NONE = 'none';

    /**
     * @var array<string>
     */
    public array $locales;

    /**
     * @deprecated use {@see $labelFormat} instead; kept so existing call sites
     *             passing `showLocaleName: false` keep rendering flags only
     */
    public bool $showLocaleName;

    /**
     * One of `name` (English), `code` (EN) or `none` (flag only).
     */
    public string $labelFormat;

    /**
     * Optional heading rendered at the top of the dropdown, e.g. "Change
     * language". Translate at the call site.
     */
    public ?string $header;

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
            'locales' => ['en', 'fr'],
            'showLocaleName' => true,
            // Derived so `showLocaleName: false` keeps meaning "flag only" for
            // call sites written before labelFormat existed. An explicit
            // labelFormat always wins.
            'labelFormat' => static fn (Options $options): string => $options['showLocaleName'] ? self::LABEL_NAME : self::LABEL_NONE,
            'header' => null,
        ]);

        $resolver->setAllowedTypes('locales', ['array']);
        $resolver->setAllowedTypes('showLocaleName', 'bool');
        $resolver->setAllowedTypes('labelFormat', 'string');
        $resolver->setAllowedValues('labelFormat', [self::LABEL_NAME, self::LABEL_CODE, self::LABEL_NONE]);
        $resolver->setAllowedTypes('header', ['string', 'null']);
    }
}
