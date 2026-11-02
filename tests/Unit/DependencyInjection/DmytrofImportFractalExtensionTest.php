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

namespace Dmytrof\ImportFractalBundle\Tests\Unit\DependencyInjection;

use Dmytrof\ImportFractalBundle\DependencyInjection\{Configuration, DmytrofImportFractalExtension};
use Dmytrof\ImportFractalBundle\DmytrofImportFractalBundle;
use Dmytrof\ImportFractalBundle\Transformer\{ImporterTransformer, ItemTransformer, ReaderTransformer, TaskTransformer};
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DmytrofImportFractalExtensionTest extends TestCase
{
    public function testAlias(): void
    {
        $this->assertSame('dmytrof_import_fractal', (new DmytrofImportFractalExtension())->getAlias());
        $this->assertInstanceOf(DmytrofImportFractalExtension::class, (new DmytrofImportFractalBundle())->getContainerExtension());
        $this->assertSame([], (new Processor())->processConfiguration(new Configuration(), [[]]));
    }

    public function testServices(): void
    {
        $container = new ContainerBuilder();
        (new DmytrofImportFractalExtension())->load([], $container);

        foreach ([ImporterTransformer::class, ReaderTransformer::class, TaskTransformer::class, ItemTransformer::class] as $id) {
            $this->assertTrue($container->hasDefinition($id), $id);
            $this->assertTrue($container->getDefinition($id)->isAutowired(), $id);
            $this->assertTrue($container->getDefinition($id)->isAutoconfigured(), $id);
        }
        // Only transformers are registered
        $this->assertCount(4, array_filter($container->getDefinitions(), static fn (string $id) => str_starts_with($id, 'Dmytrof\\'), \ARRAY_FILTER_USE_KEY));
    }
}
