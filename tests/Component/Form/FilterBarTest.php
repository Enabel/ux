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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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

    public function testTheSortIsCarriedThroughASubmit(): void
    {
        static::getContainer()->get(RequestStack::class)->push(Request::create('/admin', 'GET', ['sort' => 'actor', 'direction' => 'asc', 'page' => '2', 'q' => 'abc']));

        $crawler = $this->render($this->form());

        $this->assertSame('actor', $crawler->filter('input[type=hidden][name=sort]')->attr('value'));
        $this->assertSame('asc', $crawler->filter('input[type=hidden][name=direction]')->attr('value'));
        $this->assertCount(0, $crawler->filter('input[name=page]'), 'a new filter starts on the first page');
    }

    public function testAFormFieldNamedLikeTheSortWins(): void
    {
        static::getContainer()->get(RequestStack::class)->push(Request::create('/admin', 'GET', ['sort' => 'actor']));

        $crawler = $this->render($this->form()->add('sort', TextType::class));

        $this->assertCount(1, $crawler->filter('input[name=sort]'));
        $this->assertCount(0, $crawler->filter('input[type=hidden][name=sort]'));
    }

    public function testHiddenFieldsHaveNoColumnNorLabel(): void
    {
        $crawler = $this->render($this->form()->add('scope', HiddenType::class, ['data' => 'mine']));

        $this->assertSame('mine', $crawler->filter('input[type=hidden][name=scope]')->attr('value'));
        $this->assertCount(0, $crawler->filter('[class*="col"] input[name=scope]'));
        $this->assertCount(1, $crawler->filter('label'), 'only the label of q');
    }

    public function testFormAndLabelAttributesAreKept(): void
    {
        /** @var FormFactoryInterface $factory */
        $factory = static::getContainer()->get('test.form_factory');
        $form = $factory
            ->createNamed('', FormType::class, null, ['method' => 'GET', 'attr' => ['class' => 'filters', 'data-controller' => 'autosubmit']])
            ->add('q', TextType::class, ['label_attr' => ['class' => 'fw-bold', 'id' => 'q-label']]);

        $crawler = $this->render($form);

        $this->assertSame('filters row g-2 align-items-end', $crawler->filter('form')->attr('class'));
        $this->assertSame('autosubmit', $crawler->filter('form')->attr('data-controller'));
        $this->assertStringStartsWith('fw-bold form-label small text-body-secondary mb-1', (string) $crawler->filter('label')->attr('class'));
        $this->assertSame('q-label', $crawler->filter('label')->attr('id'));
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
