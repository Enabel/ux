<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Component\Layout;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * The base layout rendered on its own: the functions it needs from other
 * bundles (asset, importmap, component, trans) are neutral stand-ins, since
 * only the document <title> is under test.
 */
class BaseLayoutTest extends TestCase
{
    // Empties the blocks that render the favicon, the importmap, the navbar, flashes and footer.
    private const string BARE = '{% block favicon %}{% endblock %}{% block javascripts %}{% endblock %}{% block header %}{% endblock %}{% block main %}{% endblock %}{% block footer %}{% endblock %}';

    private const string BASE = "{% extends '@EnabelUx/layout/bootstrap/base.html.twig' %}".self::BARE;

    public function testTheTitleBlockFillsTheDocumentTitle(): void
    {
        $title = $this->title(['page.html.twig' => self::BASE.'{% block title %}Dashboard{% endblock %}']);

        $this->assertSame('Dashboard', $title);
    }

    public function testTheDocumentTitleDefaultsToTheApplicationName(): void
    {
        $title = $this->title(['page.html.twig' => self::BASE]);

        $this->assertSame('params.application.name', $title);
    }

    public function testAnIntermediateLayoutCanFrameThePageTitle(): void
    {
        // A layout between the base and the page (e.g. a third-party bundle's pages
        // rendered in an application layout) frames whatever title the page sets.
        $title = $this->title([
            'layout.html.twig' => self::BASE."{% block document_title %}Monitor: {{ block('title') }} · App{% endblock %}",
            'page.html.twig' => "{% extends 'layout.html.twig' %}{% block title %}Dashboard{% endblock %}",
        ]);

        $this->assertSame('Monitor: Dashboard · App', $title);
    }

    /**
     * @param array<string, string> $templates
     */
    private function title(array $templates): string
    {
        $bundle = new FilesystemLoader();
        $bundle->addPath(\dirname(__DIR__, 3).'/templates', 'EnabelUx');

        $twig = new Environment(new ChainLoader([new ArrayLoader($templates), $bundle]));
        foreach (['asset', 'importmap', 'component'] as $function) {
            $twig->addFunction(new TwigFunction($function, static fn (): string => ''));
        }
        $twig->addFilter(new TwigFilter('trans', static fn (string $key): string => $key));
        $twig->addGlobal('app', ['request' => ['locale' => 'en'], 'flashes' => []]);

        return (new Crawler($twig->render('page.html.twig')))->filter('title')->text();
    }
}
