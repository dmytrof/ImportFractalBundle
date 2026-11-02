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

namespace Dmytrof\ImportFractalBundle\Tests\Unit\Transformer;

use Dmytrof\FractalBundle\Service\FractalManager;
use Dmytrof\ImportBundle\Entity\Task\Task;
use Dmytrof\ImportBundle\Importer\ImporterInterface;
use Dmytrof\ImportBundle\Manager\ItemManager;
use Dmytrof\ImportBundle\Model\ImportStatistics;
use Dmytrof\ImportBundle\Reader\{CsvReader, ReaderInterface};
use Dmytrof\ImportFractalBundle\Tests\Fixtures\{FractalManagerFactory, TestImporter};
use Dmytrof\ImportFractalBundle\Transformer\{ImporterTransformer, ItemTransformer, ReaderTransformer, TaskTransformer};
use Dmytrof\ImportBundle\Entity\Item\Item;
use PHPUnit\Framework\TestCase;

class TransformersTest extends TestCase
{
    private FractalManager $manager;
    private Task $task;

    protected function setUp(): void
    {
        $this->manager = FractalManagerFactory::create();
        $this->task = FractalManagerFactory::createTask($this->createStub(ItemManager::class));
        (new \ReflectionProperty(Task::class, 'id'))->setValue($this->task, 7);
        $this->task->setImportedAt(new \DateTime('2026-01-02 03:04:05', new \DateTimeZone('UTC')));
    }

    public function testSubjectClasses(): void
    {
        $this->assertSame(ImporterInterface::class, $this->manager->getTransformer(ImporterTransformer::class)->getSubjectClass());
        $this->assertSame(ReaderInterface::class, $this->manager->getTransformer(ReaderTransformer::class)->getSubjectClass());
        $this->assertSame(Task::class, $this->manager->getTransformer(TaskTransformer::class)->getSubjectClass());
        $this->assertSame(Item::class, $this->manager->getTransformer(ItemTransformer::class)->getSubjectClass());
    }

    public function testTask(): void
    {
        $data = $this->manager->getModelData($this->task, TaskTransformer::class);

        $this->assertSame(7, $data['id']);
        $this->assertSame('Products', $data['title']);
        $this->assertSame('products', $data['code']);
        $this->assertFalse($data['inProgress']);
        $this->assertSame('2026-01-02T03:04:05+00:00', $data['importedAt']);
        // Data of ModelsManagementFractalBundle extensions
        $this->assertTrue($data['active']);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('updatedAt', $data);
        $this->assertArrayNotHasKey('link', $data);
        $this->assertArrayNotHasKey('importer', $data);
    }

    public function testTaskIncludes(): void
    {
        $this->task->setPaginatedLink(true)->setPageParameterInLink('p')->setFirstPageValue(0);
        $statistics = (new ImportStatistics())->setAll(3);
        (new \ReflectionMethod(Task::class, 'setImportStatistics'))->invoke($this->task, $statistics);

        $data = $this->manager->parseIncludes(['link', 'paginationData', 'importStatistics', 'importer', 'reader', 'reader.options'])
            ->getModelData($this->task, TaskTransformer::class);

        $this->assertSame('https://example.com/products.csv', $data['link']);
        // Pagination data is moved to the root
        $this->assertArrayNotHasKey('paginationData', $data);
        $this->assertTrue($data['paginatedLink']);
        $this->assertSame('p', $data['pageParameterInLink']);
        $this->assertSame(0, $data['firstPageValue']);
        $this->assertSame(3, $data['importStatistics']['all']);

        $this->assertSame(['code' => TestImporter::CODE, 'title' => TestImporter::TITLE], $data['importer']);

        $this->assertSame(CsvReader::getCode(), $data['reader']['code']);
        $this->assertSame(',', $data['reader']['options']['delimiter']);
        $this->assertFalse($data['reader']['options']['hasHeadingRow']);
    }

    public function testImporter(): void
    {
        $importer = $this->task->getImporter();

        $this->assertSame(['code' => TestImporter::CODE, 'title' => TestImporter::TITLE], $this->manager->getModelData($importer, ImporterTransformer::class));

        $data = $this->manager->parseIncludes('options')->getModelData($importer, ImporterTransformer::class);
        $this->assertSame($this->task->getImporterOptionsArr(), $data['options']);
    }

    public function testReader(): void
    {
        $reader = $this->task->getReader();

        $this->assertSame(['code' => CsvReader::getCode(), 'title' => $reader->getTitle()], $this->manager->getModelData($reader, ReaderTransformer::class));

        $data = $this->manager->parseIncludes('options')->getModelData($reader, ReaderTransformer::class);
        $this->assertSame($reader->getOptions()->toArray(), $data['options']);
    }
}
