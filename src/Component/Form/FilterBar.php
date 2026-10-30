<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Component\Form;

use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * The filters of a list: a GET form on one line, with "Filter" and a "Reset"
 * link back to the unfiltered list. A SearchType field takes the remaining
 * width and gets a search icon.
 */
class FilterBar
{
    public FormView $form;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * The current route without its query string, or the current path.
     */
    public function resetUrl(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return '';
        }

        $route = $request->attributes->get('_route');
        if (!\is_string($route) || '' === $route) {
            return $request->getPathInfo();
        }

        $params = $request->attributes->get('_route_params', []);

        return $this->urlGenerator->generate($route, \is_array($params) ? $params : []);
    }
}
