<?php

namespace Enabel\Ux\Helper;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

final readonly class Modal
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly HttpKernelInterface $httpKernel,
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param string $background The URI of the page to render in the background
     * @param string $template The template of the modal
     * @param array<mixed> $context Template context of the modal
     */
    public function render(string $background, string $template, array $context = []): Response
    {
        $request = $this->getRequest();

        $modal = $this->twig->render($template, $context);

        if ($request->query->has('_modal')) {
            return new Response($modal);
        }

        $subRequest = Request::create($background);
        $subRequest->attributes->set('_modal', ['background' => $background, 'content' => $modal]);

        if ($request->hasSession()) {
            $subRequest->setSession($request->getSession());
        }

        return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    public function redirect(Request $request, string $url): Response
    {
        $request = $this->getRequest();

        if ($request->query->has('_modal')) {
            return new Response('', 200, ['X-Modal-Redirect' => $url]);
        }

        return new RedirectResponse($url);
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