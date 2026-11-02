<?php

/*
 * This file is part of the DmytrofImportFractalBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Dmytrof\ImportFractalBundle\Tests\Functional\App;

use Composer\InstalledVersions;
use Dmytrof\FractalBundle\DmytrofFractalBundle;
use Dmytrof\ImportBundle\DmytrofImportBundle;
use Dmytrof\ImportBundle\Entity\Task\Task;
use Dmytrof\ImportFractalBundle\DmytrofImportFractalBundle;
use Dmytrof\ImportFractalBundle\Tests\Fixtures\TestImporter;
use Dmytrof\ModelsManagementBundle\DmytrofModelsManagementBundle;
use Dmytrof\ModelsManagementFractalBundle\DmytrofModelsManagementFractalBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineBundle();
        yield new MonologBundle();
        yield new DmytrofModelsManagementBundle();
        yield new DmytrofFractalBundle();
        yield new DmytrofModelsManagementFractalBundle();
        yield new DmytrofImportBundle();
        yield new DmytrofImportFractalBundle();
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/dmytrof_import_fractal_bundle_tests/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/dmytrof_import_fractal_bundle_tests/log';
    }

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $container->extension('framework', [
            'secret' => 'test',
            'test' => true,
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => ['log' => true],
            'form' => ['enabled' => true, 'csrf_protection' => false],
            'validation' => ['enabled' => true],
            'router' => ['utf8' => true, 'resource' => __DIR__.'/routes.php', 'type' => 'php'],
        ]);

        $orm = [
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'mappings' => [
                'DmytrofImportBundle' => [
                    'is_bundle' => false,
                    'type' => 'attribute',
                    'dir' => \dirname((new \ReflectionClass(Task::class))->getFileName(), 2),
                    'prefix' => 'Dmytrof\ImportBundle\Entity',
                ],
            ],
        ];
        // DoctrineBundle 2.x needs PHP 8.4 native lazy objects to be enabled explicitly (DoctrineBundle 3 always uses them)
        if (version_compare((string) InstalledVersions::getVersion('doctrine/doctrine-bundle'), '3.0.0', '<')) {
            $orm['enable_native_lazy_objects'] = true;
        }
        $container->extension('doctrine', ['dbal' => ['driver' => 'pdo_sqlite', 'memory' => true], 'orm' => $orm]);
        $container->extension('monolog', ['handlers' => ['main' => ['type' => 'null']]]);

        $services = $container->services()->defaults()->autowire()->autoconfigure();
        // Importer is collected by ImportBundle with "dmytrof.import.importer" tag
        $services->set(TestImporter::class)->tag('dmytrof.import.importer');

        // Private services used by the tests
        $services->alias('test.fractal_manager', \Dmytrof\FractalBundle\Service\FractalManager::class)->public();
        $services->alias('test.transformers_container', \Dmytrof\FractalBundle\Service\TransformersContainer::class)->public();
    }
}
