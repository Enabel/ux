<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Admin;

use Enabel\Ux\Admin\CsrfGuard;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class CsrfGuardTest extends TestCase
{
    private CsrfTokenManager $manager;

    protected function setUp(): void
    {
        $this->manager = new CsrfTokenManager(null, new class implements TokenStorageInterface {
            /** @var array<string, string> */
            private array $tokens = [];

            public function getToken(string $tokenId): string
            {
                return $this->tokens[$tokenId] ?? throw new \Symfony\Component\Security\Csrf\Exception\TokenNotFoundException();
            }

            public function setToken(string $tokenId, #[\SensitiveParameter] string $token): void
            {
                $this->tokens[$tokenId] = $token;
            }

            public function removeToken(string $tokenId): ?string
            {
                $token = $this->tokens[$tokenId] ?? null;
                unset($this->tokens[$tokenId]);

                return $token;
            }

            public function hasToken(string $tokenId): bool
            {
                return isset($this->tokens[$tokenId]);
            }
        });
    }

    public function testAValidTokenPasses(): void
    {
        $token = $this->manager->getToken('admin.thing')->getValue();

        (new CsrfGuard($this->manager))->assertValid(Request::create('/', 'POST', ['_token' => $token]), 'admin.thing');

        $this->addToAssertionCount(1);
    }

    public function testATokenForAnotherIdIsRefused(): void
    {
        $token = $this->manager->getToken('admin.other')->getValue();

        $this->expectException(AccessDeniedHttpException::class);
        (new CsrfGuard($this->manager))->assertValid(Request::create('/', 'POST', ['_token' => $token]), 'admin.thing');
    }

    public function testAMissingTokenIsRefused(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        (new CsrfGuard($this->manager))->assertValid(Request::create('/', 'POST'), 'admin.thing');
    }

    public function testWithoutCsrfProtectionItSaysWhatIsMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('symfony/security-csrf');
        (new CsrfGuard())->assertValid(Request::create('/', 'POST'), 'admin.thing');
    }
}
