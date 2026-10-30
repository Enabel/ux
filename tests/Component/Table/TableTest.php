<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Table;

use Enabel\Ux\Admin\Sort;
use Enabel\Ux\Tests\Kernel\TestKernel;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;
use Twig\Environment;

class TableTest extends KernelTestCase
{
    use InteractsWithTwigComponents;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testRowsAreRenderedFromTheRowBlock(): void
    {
        $pager = new Pagerfanta(new ArrayAdapter([
            (object) ['actor' => 'alice'],
            (object) ['actor' => 'bob'],
        ]));

        $crawler = $this->renderTemplate('<twig:Enabel:Ux:Table :pager="pager" emptyMessage="Nothing yet"><twig:block name="row"><td>{{ item.actor }}</td></twig:block></twig:Enabel:Ux:Table>', ['pager' => $pager]);

        $cells = $crawler->filter('tbody tr td');
        $this->assertCount(2, $cells);
        $this->assertSame('alice', trim($cells->eq(0)->text()));
        $this->assertSame('bob', trim($cells->eq(1)->text()));
    }

    public function testRendersTheCardWithFiltersTableRangeAndPagination(): void
    {
        $this->pushRequest([]);
        $pager = new Pagerfanta(new ArrayAdapter(range(1, 30)));
        $pager->setMaxPerPage(25);

        $crawler = $this->renderTemplate('<twig:Enabel:Ux:Table :pager="pager"><twig:block name="filters"><span id="f">FILTERS</span></twig:block><twig:block name="row"><td>{{ item }}</td></twig:block></twig:Enabel:Ux:Table>', ['pager' => $pager]);

        $this->assertCount(1, $crawler->filter('div.card.border-0.shadow-sm'));
        $this->assertCount(1, $crawler->filter('.card > .card-header.bg-body #f'));
        $this->assertCount(1, $crawler->filter('.card > .table-responsive > table.table.table-hover.mb-0'));
        $this->assertCount(1, $crawler->filter('thead.table-light'));
        $this->assertSame('1–25 of 30', preg_replace('/\s+/', ' ', trim($crawler->filter('.card-footer span')->first()->text())));
        $this->assertCount(1, $crawler->filter('.card-footer ul.pagination.mb-0'));
    }

    public function testCardHasNoHeaderWithoutFiltersAndNoPaginationOnOnePage(): void
    {
        $crawler = new Crawler((string) $this->renderTwigComponent('Enabel:Ux:Table', [
            'pager' => new Pagerfanta(new ArrayAdapter([1, 2, 3])),
        ]));

        $this->assertCount(0, $crawler->filter('.card-header'));
        $this->assertCount(0, $crawler->filter('.pagination'));
        $this->assertStringContainsString('1–3 of 3', $crawler->filter('.card-footer')->text());
    }

    public function testEmptyPagerShowsTheMessageAndNoRange(): void
    {
        $crawler = new Crawler((string) $this->renderTwigComponent('Enabel:Ux:Table', [
            'pager' => new Pagerfanta(new ArrayAdapter([])),
            'emptyMessage' => 'Nothing yet',
        ]));

        $this->assertSame('Nothing yet', trim($crawler->filter('tbody td')->text()));
        $this->assertStringNotContainsString('–', $crawler->filter('.card-footer')->text());
    }

    public function testSortedHeaderLinksToTheOppositeDirectionAndTheFirstPage(): void
    {
        $this->pushRequest(['sort' => 'actor', 'direction' => 'asc', 'type' => 'X', 'page' => '3']);

        $th = $this->header(['label' => 'Actor', 'field' => 'actor', 'sort' => new Sort('actor', 'ASC')]);

        $this->assertSame('ascending', $th->attr('aria-sort'));
        $this->assertSame('/admin?sort=actor&direction=desc&type=X', $th->filter('a')->attr('href'));
        $this->assertStringContainsString('↑', $th->text());
    }

    public function testDescendingHeaderLinksAscending(): void
    {
        $this->pushRequest(['sort' => 'actor', 'direction' => 'desc']);

        $th = $this->header(['label' => 'Actor', 'field' => 'actor', 'sort' => new Sort('actor', 'DESC')]);

        $this->assertSame('descending', $th->attr('aria-sort'));
        $this->assertStringContainsString('direction=asc', (string) $th->filter('a')->attr('href'));
        $this->assertStringContainsString('↓', $th->text());
    }

    public function testUnsortedHeaderLinksDescending(): void
    {
        $this->pushRequest(['sort' => 'actor', 'direction' => 'asc']);

        $th = $this->header(['label' => 'Subject', 'field' => 'subject', 'sort' => new Sort('actor', 'ASC')]);

        $this->assertNull($th->attr('aria-sort'));
        $this->assertSame('/admin?sort=subject&direction=desc', $th->filter('a')->attr('href'));
    }

    public function testRouteParametersAreKept(): void
    {
        $request = Request::create('/admin/users/items');
        $request->attributes->set('_route', 'admin_items');
        $request->attributes->set('_route_params', ['section' => 'users']);
        static::getContainer()->get(RequestStack::class)->push($request);

        $th = $this->header(['label' => 'Name', 'field' => 'name', 'sort' => new Sort('name', 'ASC')]);

        $this->assertSame('/admin/users/items?sort=name&direction=desc', $th->filter('a')->attr('href'));
    }

    public function testAQueryParameterNamedLikeAPlaceholderDoesNotChangeTheList(): void
    {
        $request = Request::create('/admin/users/items', 'GET', ['section' => 'other']);
        $request->attributes->set('_route', 'admin_items');
        $request->attributes->set('_route_params', ['section' => 'users']);
        static::getContainer()->get(RequestStack::class)->push($request);

        $th = $this->header(['label' => 'Name', 'field' => 'name', 'sort' => new Sort('name', 'ASC')]);

        $this->assertStringStartsWith('/admin/users/items?', (string) $th->filter('a')->attr('href'));
    }

    public function testTheComputedSortWinsOverARouteDefault(): void
    {
        $request = Request::create('/admin/sorted/desc', 'GET', ['page' => '2']);
        $request->attributes->set('_route', 'admin_sorted');
        $request->attributes->set('_route_params', ['direction' => 'desc']);
        static::getContainer()->get(RequestStack::class)->push($request);

        $th = $this->header(['label' => 'Name', 'field' => 'name', 'sort' => new Sort('name', 'DESC')]);

        $this->assertSame('/admin/sorted/asc?sort=name', $th->filter('a')->attr('href'));
    }

    public function testARequiredPagePlaceholderIsKept(): void
    {
        $request = Request::create('/admin/paged/3');
        $request->attributes->set('_route', 'admin_paged');
        $request->attributes->set('_route_params', ['page' => '3']);
        static::getContainer()->get(RequestStack::class)->push($request);

        $th = $this->header(['label' => 'Name', 'field' => 'name', 'sort' => new Sort('name', 'ASC')]);

        $this->assertSame('/admin/paged/3?sort=name&direction=desc', $th->filter('a')->attr('href'));
    }

    public function testArrayQueryValuesArePreserved(): void
    {
        $this->pushRequest(['type' => ['a', 'b']]);

        $th = $this->header(['label' => 'Actor', 'field' => 'actor', 'sort' => new Sort('actor', 'ASC')]);

        $this->assertSame('/admin?type%5B0%5D=a&type%5B1%5D=b&sort=actor&direction=desc', $th->filter('a')->attr('href'));
    }

    public function testHeaderWithoutRouteIsPlain(): void
    {
        static::getContainer()->get(RequestStack::class)->push(Request::create('/anywhere'));

        $th = $this->header(['label' => 'Actor', 'field' => 'actor', 'sort' => new Sort('actor', 'ASC')]);

        $this->assertCount(0, $th->filter('a'));
        $this->assertSame('Actor', trim($th->text()));
    }

    public function testHeaderWithoutFieldIsPlain(): void
    {
        $this->pushRequest([]);

        $th = $this->header(['label' => 'Plain']);

        $this->assertCount(0, $th->filter('a'));
        $this->assertSame('Plain', trim($th->text()));
    }

    /**
     * @param array<string, mixed> $context
     */
    private function renderTemplate(string $template, array $context): Crawler
    {
        /** @var Environment $twig */
        $twig = static::getContainer()->get('twig');

        return new Crawler($twig->createTemplate($template)->render($context));
    }

    /**
     * @param array<string, mixed> $props
     */
    private function header(array $props): Crawler
    {
        $html = (string) $this->renderTwigComponent('Enabel:Ux:TableHeader', $props);

        return (new Crawler("<table><tr>{$html}</tr></table>"))->filter('th');
    }

    /**
     * @param array<string, mixed> $query
     */
    private function pushRequest(array $query): void
    {
        $request = Request::create('/admin', 'GET', $query);
        $request->attributes->set('_route', 'admin');
        $request->attributes->set('_route_params', []);
        static::getContainer()->get(RequestStack::class)->push($request);
    }
}
