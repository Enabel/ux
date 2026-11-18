<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Helper;

use Enabel\Ux\Helper\Modal;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\AccessMap;
use Twig\Environment;

class ModalTest extends TestCase
{
    private RequestStack $requestStack;
    private HttpKernelInterface $httpKernel;
    private Environment $twig;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        // Stubs: ces dépendances ne sont pas sollicitées par les scénarios redirect/callback.
        $this->httpKernel = $this->createStub(HttpKernelInterface::class);
        $this->twig = $this->createStub(Environment::class);
    }

    public function testRenderWithModalQueryParameter(): void
    {
        $request = new Request(['_enabel_ux_modal' => '1']);
        $this->requestStack->push($request);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('modal.html.twig', ['key' => 'value'])
            ->willReturn('<div>Modal Content</div>');

        $modal = new Modal($this->requestStack, $this->httpKernel, $twig);
        $response = $modal->render('/background', 'modal.html.twig', ['key' => 'value']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('<div>Modal Content</div>', $response->getContent());
    }

    public function testRenderWithoutModalQueryParameter(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('modal.html.twig', [])
            ->willReturn('<div>Modal Content</div>');

        $expectedResponse = new Response('Background with modal');

        $httpKernel = $this->createMock(HttpKernelInterface::class);
        $httpKernel->expects($this->once())
            ->method('handle')
            ->willReturn($expectedResponse);

        $modal = new Modal($this->requestStack, $httpKernel, $twig);
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

    #[DataProvider('foreignBackgroundProvider')]
    public function testRenderRejectsABackgroundOutsideTheApplication(string $backgroundUri): void
    {
        $this->requestStack->push(new Request());
        $kernel = $this->recordingKernel();

        try {
            (new Modal($this->requestStack, $kernel, $this->twig))->render($backgroundUri, 'modal.html.twig');
            $this->fail('A background outside the application must be refused.');
        } catch (BadRequestHttpException) {
            $this->assertNull($kernel->request);
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function foreignBackgroundProvider(): iterable
    {
        yield 'absolute URL' => ['https://evil.example/admin'];
        yield 'protocol-relative URL' => ['//evil.example/admin'];
        yield 'backslash host' => ['/\\evil.example/admin'];
        yield 'relative path' => ['admin'];
        yield 'empty' => [''];
        yield 'control character' => ["/admin\t"];
    }

    public function testRenderDeniesABackgroundTheUserCannotAccess(): void
    {
        $this->requestStack->push(new Request());
        $kernel = $this->recordingKernel();

        $this->expectException(AccessDeniedException::class);

        try {
            $this->securedModal($kernel, ['ROLE_USER'])->render('/admin/users', 'modal.html.twig');
        } finally {
            $this->assertNull($kernel->request);
        }
    }

    public function testRenderDeniesAProtectedBackgroundToAnAnonymousUser(): void
    {
        $this->requestStack->push(new Request());
        $kernel = $this->recordingKernel();

        $this->expectException(AccessDeniedException::class);

        $this->securedModal($kernel, null)->render('/admin/users', 'modal.html.twig');
    }

    public function testRenderAllowsABackgroundWhenTheUserHoldsOneOfTheRoles(): void
    {
        $this->requestStack->push(new Request());
        $kernel = $this->recordingKernel();

        $this->securedModal($kernel, ['ROLE_AUDITOR'])->render('/admin/users', 'modal.html.twig');

        $this->assertSame('/admin/users', $kernel->request?->getPathInfo());
    }

    public function testRenderAllowsAPublicBackgroundToAnAnonymousUser(): void
    {
        $this->requestStack->push(new Request());
        $kernel = $this->recordingKernel();

        $this->securedModal($kernel, null)->render('/login', 'modal.html.twig');

        $this->assertSame('/login', $kernel->request?->getPathInfo());
    }

    public function testRenderRequestsTheBackgroundOnTheHostSchemeAndClientOfTheCurrentRequest(): void
    {
        $this->requestStack->push(Request::create('https://grant.example/en/modal', 'POST', server: [
            'REMOTE_ADDR' => '203.0.113.7',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ]));
        $kernel = $this->recordingKernel();

        (new Modal($this->requestStack, $kernel, $this->twig))->render('/en/list?page=2', 'modal.html.twig');

        $background = $kernel->request;
        $this->assertNotNull($background);
        $this->assertSame('https://grant.example/en/list?page=2', $background->getUri());
        $this->assertSame('GET', $background->getMethod());
        $this->assertSame('203.0.113.7', $background->getClientIp());
        $this->assertNull($background->headers->get('Content-Type'));
    }

    public function testRenderNeverLetsTheBackgroundMakeTheResponseCacheable(): void
    {
        $this->requestStack->push(new Request());
        $kernel = $this->recordingKernel(new Response('page', 200, ['Cache-Control' => 'public, max-age=60, s-maxage=60']));

        $response = (new Modal($this->requestStack, $kernel, $this->twig))->render('/list', 'modal.html.twig');

        $this->assertFalse($response->isCacheable());
        $this->assertTrue($response->headers->hasCacheControlDirective('no-store'));
        $this->assertFalse($response->headers->hasCacheControlDirective('public'));
        $this->assertFalse($response->headers->hasCacheControlDirective('s-maxage'));
    }

    /**
     * @param list<string>|null $roles the roles of the logged-in user, null for an anonymous visitor
     */
    private function securedModal(HttpKernelInterface $kernel, ?array $roles): Modal
    {
        $accessMap = new AccessMap();
        $accessMap->add(new PathRequestMatcher('^/login'), [AuthenticatedVoter::PUBLIC_ACCESS]);
        $accessMap->add(new PathRequestMatcher('^/admin'), ['ROLE_ADMIN', 'ROLE_AUDITOR']);

        $tokenStorage = new TokenStorage();
        if (null !== $roles) {
            $tokenStorage->setToken(new UsernamePasswordToken(new InMemoryUser('user', null, $roles), 'main', $roles));
        }

        return new Modal(
            $this->requestStack,
            $kernel,
            $this->twig,
            $accessMap,
            new AccessDecisionManager([new RoleVoter()]),
            $tokenStorage,
        );
    }

    /**
     * A kernel that keeps the background request it is given.
     */
    private function recordingKernel(?Response $response = null): HttpKernelInterface
    {
        return new class($response ?? new Response('page')) implements HttpKernelInterface {
            public ?Request $request = null;

            public function __construct(private readonly Response $response)
            {
            }

            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                $this->request = $request;

                return $this->response;
            }
        };
    }
}
