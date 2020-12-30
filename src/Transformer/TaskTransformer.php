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
            'link' => $subject->getLink(),
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
}