<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Helper;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\AccessMapInterface;
use Twig\Environment;

final readonly class Modal
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly HttpKernelInterface $httpKernel,
        private readonly Environment $twig,
        private readonly ?AccessMapInterface $accessMap = null,
        private readonly ?AccessDecisionManagerInterface $accessDecisionManager = null,
        private readonly ?TokenStorageInterface $tokenStorage = null,
    ) {
    }

    /**
     * The background page is rendered in a sub-request, which the Symfony
     * firewall skips: its access_control rules are checked here instead.
     *
     * @param string       $backgroundUri A path of this application (/...), never a URL with a scheme or a host
     * @param string       $template      The template of the modal
     * @param array<mixed> $context       Template context of the modal
     */
    public function render(string $backgroundUri, string $template, array $context = []): Response
    {
        $request = $this->getRequest();

        $modal = $this->twig->render($template, $context);

        if ($request->query->get('_enabel_ux_modal')) {
            return new Response($modal);
        }

        $subRequest = $this->createBackgroundRequest($request, $backgroundUri);
        $this->denyAccessUnlessGranted($subRequest);

        $subRequest->attributes->set('_enabel_ux_modal', ['content' => $modal, 'backgroundUri' => $backgroundUri]);

        if ($request->hasSession()) {
            $subRequest->setSession($request->getSession());
        }

        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        // The response holds the modal of this user: it must never be stored by a shared cache,
        // whatever the background page declares.
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }

    public function redirect(string $url): Response
    {
        $request = $this->getRequest();

        if ($request->query->get('_enabel_ux_modal')) {
            return new Response('', 200, ['X-Modal-Redirect' => $url]);
        }

        return new RedirectResponse($url);
    }

    public function redirectModal(string $url): Response
    {
        $request = $this->getRequest();

        if ($request->query->get('_enabel_ux_modal')) {
            return new Response('', 200, ['X-Modal-Redirect-Self' => $url]);
        }

        return new RedirectResponse($url);
    }

    public function callback(string $payload): Response
    {
        return new Response('', 204, ['X-Modal-Callback' => $payload]);
    }

    /**
     * The background is requested on the host, scheme and client address of the
     * current request, so host-, scheme- or IP-based rules see the real caller.
     */
    private function createBackgroundRequest(Request $request, string $backgroundUri): Request
    {
        // A path of this site only: no scheme, no //host or /\host, no control character.
        if (1 !== preg_match('#^/(?![/\\\\])[^\x00-\x20\x7f]*\z#', $backgroundUri)) {
            throw new BadRequestHttpException('The modal background must be a path of this application.');
        }

        $server = $request->server->all();
        unset($server['CONTENT_TYPE'], $server['CONTENT_LENGTH'], $server['HTTP_CONTENT_TYPE'], $server['HTTP_CONTENT_LENGTH']);

        return Request::create($backgroundUri, 'GET', [], [], [], $server);
    }

    /**
     * Mirrors Symfony's AccessListener, which does not run for a sub-request.
     */
    private function denyAccessUnlessGranted(Request $subRequest): void
    {
        if (null === $this->accessMap || null === $this->accessDecisionManager) {
            return;
        }

        [$attributes] = $this->accessMap->getPatterns($subRequest);

        if (!$attributes || [AuthenticatedVoter::PUBLIC_ACCESS] === $attributes) {
            return;
        }

        $token = $this->tokenStorage?->getToken() ?? new NullToken();

        // access_control grants the access when the user holds one of the attributes of the rule.
        foreach ($attributes as $attribute) {
            if ($this->accessDecisionManager->decide($token, [$attribute], $subRequest)) {
                return;
            }
        }

        $exception = new AccessDeniedException();
        $exception->setAttributes($attributes);
        $exception->setSubject($subRequest);

        throw $exception;
    }

    private function getRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \RuntimeException('No request context available');
        }

        return $request;
    }
}
