<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Helper;

use Enabel\Ux\Helper\Modal;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

class ModalTest extends TestCase
{
    private RequestStack $requestStack;
    private HttpKernelInterface $httpKernel;
    private Environment $twig;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
        $this->twig = $this->createMock(Environment::class);
    }

    public function testRenderWithModalQueryParameter(): void
    {
        $request = new Request(['_enabel_ux_modal' => '1']);
        $this->requestStack->push($request);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('modal.html.twig', ['key' => 'value'])
            ->willReturn('<div>Modal Content</div>');

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->render('/background', 'modal.html.twig', ['key' => 'value']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('<div>Modal Content</div>', $response->getContent());
    }

    public function testRenderWithoutModalQueryParameter(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('modal.html.twig', [])
            ->willReturn('<div>Modal Content</div>');

        $expectedResponse = new Response('Background with modal');

        $this->httpKernel->expects($this->once())
            ->method('handle')
            ->willReturn($expectedResponse);

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->render('/background', 'modal.html.twig');

        $this->assertSame($expectedResponse, $response);
    }

    public function testRedirectWithModalQueryParameter(): void
    {
        $request = new Request(['_enabel_ux_modal' => '1']);
        $this->requestStack->push($request);

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->redirect('/target-url');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/target-url', $response->headers->get('X-Modal-Redirect'));
        $this->assertEquals('', $response->getContent());
    }

    public function testRedirectWithoutModalQueryParameter(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->redirect('/target-url');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/target-url', $response->getTargetUrl());
    }

    public function testRedirectModalWithModalQueryParameter(): void
    {
        $request = new Request(['_enabel_ux_modal' => '1']);
        $this->requestStack->push($request);

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->redirectModal('/modal-url');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/modal-url', $response->headers->get('X-Modal-Redirect-Self'));
        $this->assertEquals('', $response->getContent());
    }

    public function testRedirectModalWithoutModalQueryParameter(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->redirectModal('/modal-url');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/modal-url', $response->getTargetUrl());
    }

    public function testCallback(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);
        $response = $modal->callback('{"action": "close"}');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('{"action": "close"}', $response->headers->get('X-Modal-Callback'));
        $this->assertEquals('', $response->getContent());
    }

    public function testGetRequestThrowsExceptionWhenNoRequestAvailable(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No request context available');

        $modal = new Modal($this->requestStack, $this->httpKernel, $this->twig);

        // Call a method that uses getRequest() without pushing a request
        $modal->redirect('/url');
    }
}
