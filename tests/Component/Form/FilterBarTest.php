<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Form;

use Enabel\Ux\Tests\Kernel\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

class FilterBarTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testRendersAGetFormWithFilterAndReset(): void
    {
        $request = Request::create('/admin', 'GET', ['q' => 'abc', 'page' => '2']);
        $request->attributes->set('_route', 'admin');
        $request->attributes->set('_route_params', []);
        static::getContainer()->get(RequestStack::class)->push($request);

        $crawler = $this->render($this->form());

        $this->assertCount(1, $crawler->filter('form[method=get]'));
        $this->assertCount(1, $crawler->filter('input[name=q]'));
        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $this->assertCount(0, $crawler->filter('input[type=hidden]'));
        $this->assertSame('/admin', $crawler->filter('a.btn-link')->attr('href'));
    }

    public function testResetFallsBackToThePathWithoutRoute(): void
    {
        static::getContainer()->get(RequestStack::class)->push(Request::create('/somewhere', 'GET', ['q' => 'abc']));

        $this->assertSame('/somewhere', $this->render($this->form())->filter('a.btn-link')->attr('href'));
    }

    public function testSearchFieldTakesTheRemainingWidthWithAHiddenLabel(): void
    {
        $form = $this->form()->add('s', SearchType::class, ['label' => 'Find me']);

        $crawler = $this->render($form);

        $this->assertCount(1, $crawler->filter('.col .input-group input[name=s]'));
        $this->assertCount(1, $crawler->filter('.col .input-group svg[aria-hidden=true]'));
        $this->assertCount(1, $crawler->filter('label.visually-hidden'));
        $this->assertCount(1, $crawler->filter('.col-lg-auto input[name=q]'));
    }

    public function testFieldErrorsAreShown(): void
    {
        $form = $this->form();
        $form->get('q')->addError(new FormError('Broken'));

        $this->assertStringContainsString('Broken', $this->render($form)->html());
    }

    private function form(): FormInterface
    {
        /** @var FormFactoryInterface $factory */
        $factory = static::getContainer()->get('test.form_factory');

        return $factory
            ->createNamed('', FormType::class, null, ['method' => 'GET'])
            ->add('q', TextType::class, ['label' => 'Plain']);
    }

    private function render(FormInterface $form): Crawler
    {
        return new Crawler((string) $this->renderTwigComponent('Enabel:Ux:FilterBar', ['form' => $form->createView()]));
    }
}
