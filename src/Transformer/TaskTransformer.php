<?php

/*
 * This file is part of the DmytrofImportFractalBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ImportFractalBundle\Transformer;

use Dmytrof\ImportBundle\Model\Task as Model;
use Dmytrof\FractalBundle\Transformer\AbstractTransformer;
use League\Fractal\Resource\ResourceInterface;

class TaskTransformer extends AbstractTransformer
{
    protected const SUBJECT_CLASS = Model::class;

    protected $availableIncludes = [
        'importStatistics',
        'link',
        'paginationData',
        'importer',
        'reader',
    ];

    protected $includesToRoot = [
        'paginationData',
    ];

    /**
     * Transforms task to array
     * @param Model $subject
     * @return array
     */
    public function transformSubject($subject): array
    {
        return [
            'id' => $subject->getId(),
            'title' => $subject->getTitle(),
            'code' => $subject->getCode(),
            'inProgress' => $subject->isInProgress(),
            'importedAt' => $this->transformDateTime($subject->getImportedAt()),
        ];
    }

    /**
     * Includes import statistics
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeImportStatistics(Model $subject): ResourceInterface
    {
        return $this->primitive($subject->getImportStatisticsArr());
    }

    /**
     * Includes link
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeLink(Model $subject): ResourceInterface
    {
        return $this->primitive($subject->getLink());
    }

    /**
     * Includes link
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includePaginationData(Model $subject): ResourceInterface
    {
        return $this->primitive([
            'paginatedLink' => $subject->isPaginatedLink(),
            'pageParameterInLink' => $subject->getPageParameterInLink(),
            'firstPageValue' => $subject->getFirstPageValue(),
        ]);
    }

    /**
     * Includes importer
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeImporter(Model $subject): ResourceInterface
    {
        return $this->item($subject->getImporter(), ImporterTransformer::class);
    }

    /**
     * Includes reader
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeReader(Model $subject): ResourceInterface
    {
        return $this->item($subject->getReader(), ReaderTransformer::class);
    }
}