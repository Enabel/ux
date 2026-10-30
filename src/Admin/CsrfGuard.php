<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Checks the token posted by Enabel:Ux:ConfirmAction (field `_token`).
 *
 * A missing or invalid token is a 403: a one-click action has no form to
 * show an error in, and a silent redirect would hide a forged request.
 */
final readonly class CsrfGuard
{
    public function __construct(private ?CsrfTokenManagerInterface $tokenManager = null)
    {
    }

    public function assertValid(Request $request, string $tokenId): void
    {
        if (null === $this->tokenManager) {
            throw new \LogicException('CsrfGuard needs symfony/security-csrf and CSRF protection enabled (framework.csrf_protection).');
        }

        $token = $request->request->get('_token');

        if (!\is_string($token) || !$this->tokenManager->isTokenValid(new CsrfToken($tokenId, $token))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    }
}
