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

namespace Dmytrof\ImportFractalBundle\Tests\Functional;

use Dmytrof\FractalBundle\Service\{FractalManager, TransformersContainer};
use Dmytrof\ImportBundle\Entity\Item\Item;
use Dmytrof\ImportBundle\Entity\Task\Task;
use Dmytrof\ImportBundle\Manager\{ItemManager, TaskManager};
use Dmytrof\ImportBundle\Reader\JsonReader;
use Dmytrof\ImportFractalBundle\Tests\Fixtures\TestImporter;
use Dmytrof\ImportFractalBundle\Tests\Functional\App\TestKernel;
use Dmytrof\ImportFractalBundle\Transformer\{ImporterTransformer, ItemTransformer, ReaderTransformer, TaskTransformer};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * The bundle in a real kernel with ImportBundle, FractalBundle, ModelsManagement(Fractal)Bundle and Doctrine (SQLite)
 */
class BundleIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new TestKernel('test', true);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine.orm.entity_manager');
        (new SchemaTool($this->em))->createSchema($this->em->getMetadataFactory()->getAllMetadata());
    }

    private function fractal(): FractalManager
    {
        return clone static::getContainer()->get('test.fractal_manager');
    }

    private function createTask(): Task
    {
        $manager = static::getContainer()->get(TaskManager::class);
        $task = $manager->new()
            ->setTitle('Products')
            ->setCode('products')
            ->setLink('https://example.com/products.json')
            ->setImporterCode(TestImporter::CODE)
            ->setReaderCode(JsonReader::getCode())
            ->setPeriod(3600)
        ;
        $manager->save($task);

        return $task;
    }

    public function testTransformersAreRegistered(): void
    {
        $transformers = static::getContainer()->get('test.transformers_container');
        $this->assertInstanceOf(TransformersContainer::class, $transformers);

        foreach ([ImporterTransformer::class, ReaderTransformer::class, TaskTransformer::class, ItemTransformer::class] as $class) {
            $this->assertTrue($transformers->has($class), $class);
        }
    }

    public function testTaskFromDatabase(): void
    {
        $id = $this->createTask()->getId();
        $this->em->clear();

        $task = static::getContainer()->get(TaskManager::class)->getTask($id);
        $data = $this->fractal()->parseIncludes(['importer', 'reader', 'paginationData'])->getModelData($task, TaskTransformer::class);

        $this->assertSame($id, $data['id']);
        $this->assertSame('Products', $data['title']);
        $this->assertTrue($data['active']);
        $this->assertNotNull($data['createdAt']);
        $this->assertFalse($data['paginatedLink']);
        $this->assertSame(1, $data['firstPageValue']);
        $this->assertSame(['code' => TestImporter::CODE, 'title' => TestImporter::TITLE], $data['importer']);
        $this->assertSame(JsonReader::getCode(), $data['reader']['code']);
    }

    public function testItem(): void
    {
        $task = $this->createTask();
        $itemManager = static::getContainer()->get(ItemManager::class);
        $item = $itemManager->getNewImportedItem($task->getId(), 'sku-1')
            ->setData(['title' => 'Apple', 'price' => '1.5'])
            ->setStatusId(Item::STATUS_SCHEDULED)
        ;
        $item->setErrors(['Price is too low']);
        $itemManager->save($item);
        $itemId = $item->getId();
        $this->em->clear();

        $item = $itemManager->getItem($itemId);
        $data = $this->fractal()->parseIncludes(['data', 'formData', 'entryId', 'task'])->getModelData($item, ItemTransformer::class);

        $this->assertSame($itemId, $data['id']);
        $this->assertSame($task->getId(), $data['taskId']);
        $this->assertSame($task->getModelTitle(), $data['taskTitle']);
        $this->assertSame(Item::STATUS_SCHEDULED, $data['statusId']);
        $this->assertSame($item->getStatusTitle(), $data['statusTitle']);
        $this->assertSame(['Price is too low'], $data['errors']);
        $this->assertSame(['title' => 'Apple', 'price' => '1.5'], $data['data']);
        $this->assertSame(['title' => 'Apple', 'price' => '1.5'], $data['formData']);
        $this->assertSame('sku-1', $data['entryId']);
        $this->assertSame($task->getId(), $data['task']['id']);
        $this->assertSame('Products', $data['task']['title']);
    }
}
