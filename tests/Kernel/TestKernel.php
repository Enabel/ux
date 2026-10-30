<?php

/*
 * This file is part of the Enabel UX package.
 * Copyright (c) Enabel <https://enabel.be/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enabel\Ux\Tests\Kernel;

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Enabel\Ux\EnabelUxBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\TwigComponent\TwigComponentBundle;

/**
 * A minimal application rendering the components that need a request, a
 * router, forms or a pager.
 */
final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new TwigComponentBundle();
        yield new BabDevPagerfantaBundle();
        yield new EnabelUxBundle();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/enabel-ux/'.md5(__FILE__).'/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/enabel-ux/'.md5(__FILE__).'/log';
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => 'test',
            'http_method_override' => false,
            'default_locale' => 'en',
            'translator' => ['fallbacks' => ['en']],
            'form' => ['enabled' => true],
            'csrf_protection' => false,
            'router' => ['utf8' => true],
        ]);

        $container->extension('twig_component', [
            'anonymous_template_directory' => 'components/',
            'defaults' => [],
        ]);

        // Unused by the bundle, so removed from the container: public for the tests to build forms.
        $container->services()->alias('test.form_factory', 'form.factory')->public();
    }

    protected function build(ContainerBuilder $container): void
    {
        // TwigBundle only loads its Form integration when symfony/form is a non-dev
        // requirement, which it cannot be in a bundle: load it for the FilterBar tests.
        $twigBundleConfig = \dirname((string) (new \ReflectionClass(TwigBundle::class))->getFileName()).'/Resources/config';
        (new PhpFileLoader($container, new FileLocator($twigBundleConfig)))->load('form.php');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('admin', '/admin');
        $routes->add('admin_items', '/admin/{section}/items');
        $routes->add('admin_sorted', '/admin/sorted/{direction}')->defaults(['direction' => 'desc']);
    }
}
