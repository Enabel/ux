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

class EmailLayout
{
    public ?string $logo;
    public string $address;
    public string $addressLine;
    public ?string $noreply;
    public int $copyrightYear;
    public string $copyrightHolder;

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

        if (null === $resolved['copyrightYear']) {
            $resolved['copyrightYear'] = (int) date('Y');
        }

        return $resolved + $data;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setIgnoreUndefined();
        $resolver->setDefaults([
            'logo' => '@images/enabel-logo-email.png',
            'address' => 'Belgian Development Agency',
            'addressLine' => 'Rue Haute 147 - 1000 Brussels',
            'noreply' => 'Responses to this e-mail will not be read.',
            'copyrightYear' => null,
            'copyrightHolder' => 'Enabel',
        ]);

        $resolver->setAllowedTypes('logo', ['string', 'null']);
        $resolver->setAllowedTypes('address', 'string');
        $resolver->setAllowedTypes('addressLine', 'string');
        $resolver->setAllowedTypes('noreply', ['string', 'null']);
        $resolver->setAllowedTypes('copyrightYear', ['int', 'null']);
        $resolver->setAllowedTypes('copyrightHolder', 'string');
    }
}
