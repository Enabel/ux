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

class ErrorPage
{
    private static ?string $defaultLogo = null;
    private static ?string $defaultSymbol = null;

    public int $statusCode;
    public string $title;
    public string $message;
    public ?string $details;
    public ?string $backUrl;
    public string $backLabel;
    public string $symbol;
    public string $logo;
    public string $locale;
    public string $appName;
    public string $stylesheet;

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
        $resolver->setRequired(['statusCode', 'title', 'message']);
        $resolver->setDefaults([
            'details' => null,
            'backUrl' => null,
            'backLabel' => 'Back to homepage',
            'symbol' => self::defaultSymbol(),
            'logo' => self::defaultLogo(),
            'locale' => 'en',
            'appName' => 'Enabel',
            'stylesheet' => 'vendor/@enabel/enabel-bootstrap-theme/dist/css/error.min.css',
        ]);

        $resolver->setAllowedTypes('statusCode', 'int');
        $resolver->setAllowedTypes('title', 'string');
        $resolver->setAllowedTypes('message', 'string');
        $resolver->setAllowedTypes('details', ['string', 'null']);
        $resolver->setAllowedTypes('backUrl', ['string', 'null']);
        $resolver->setAllowedTypes('backLabel', 'string');
        $resolver->setAllowedTypes('symbol', 'string');
        $resolver->setAllowedTypes('logo', 'string');
        $resolver->setAllowedTypes('locale', 'string');
        $resolver->setAllowedTypes('appName', 'string');
        $resolver->setAllowedTypes('stylesheet', 'string');
    }

    private static function defaultLogo(): string
    {
        return self::$defaultLogo ??= trim((string) file_get_contents(__DIR__.'/Defaults/error_page_logo.txt'));
    }

    private static function defaultSymbol(): string
    {
        return self::$defaultSymbol ??= trim((string) file_get_contents(__DIR__.'/Defaults/error_page_symbol.txt'));
    }
}
