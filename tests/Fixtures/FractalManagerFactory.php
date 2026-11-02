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

namespace Dmytrof\ImportFractalBundle\Tests\Fixtures;

use Dmytrof\FractalBundle\Service\{ExtensionsContainer, FractalManager, TransformersContainer};
use Dmytrof\ImportBundle\Entity\Task\Task;
use Dmytrof\ImportBundle\Manager\ItemManager;
use Dmytrof\ImportBundle\Reader\{CsvReader, JsonReader};
use Dmytrof\ImportBundle\Service\{ImportersContainer, ReadersContainer};
use Dmytrof\ImportFractalBundle\Transformer\{ImporterTransformer, ItemTransformer, ReaderTransformer, TaskTransformer};
use Dmytrof\ModelsManagementFractalBundle\Transformer\Extension\{ActiveModelExtension, SimpleModelExtension, SortableModelExtension, TargetedModelExtension, TimestampableModelExtension};

/**
 * Wires FractalManager with ModelsManagementFractalBundle extensions and the bundle transformers, without a kernel
 */
final class FractalManagerFactory
{
    public static function create(): FractalManager
    {
        $extensions = new ExtensionsContainer([
            new SimpleModelExtension(),
            new ActiveModelExtension(),
            new SortableModelExtension(),
            new TimestampableModelExtension(),
            new TargetedModelExtension(),
        ]);
        $transformers = array_map(
            static fn (string $class) => new $class($extensions),
            [ImporterTransformer::class, ReaderTransformer::class, TaskTransformer::class, ItemTransformer::class],
        );

        return new FractalManager(new TransformersContainer($transformers));
    }

    public static function createTask(ItemManager $itemManager): Task
    {
        $translator = new IdentityTranslator();
        // Containers are set before importer/reader codes (as Doctrine postLoad listener of ImportBundle does)
        $task = new Task();
        $task->setImportersContainer(new ImportersContainer($translator, $itemManager, [new TestImporter()]));
        $task->setReadersContainer(new ReadersContainer([new CsvReader($translator), new JsonReader($translator)]));
        $task
            ->setTitle('Products')
            ->setCode('products')
            ->setLink('https://example.com/products.csv')
            ->setImporterCode(TestImporter::CODE)
            ->setReaderCode(CsvReader::getCode())
            ->setPeriod(3600)
        ;

        return $task;
    }
}
