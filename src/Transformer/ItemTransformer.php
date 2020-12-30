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

use Dmytrof\ImportBundle\Entity\Item\Item as Model;
use Dmytrof\FractalBundle\Transformer\AbstractTransformer;
use League\Fractal\Resource\ResourceInterface;

class ItemTransformer extends AbstractTransformer
{
    protected const SUBJECT_CLASS = Model::class;

    protected $availableIncludes = [
        'data',
        'formData',
        'task',
        'entryId',
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
            'taskId' => $subject->getTaskId(),
            'taskTitle' => $subject->getTaskTitle(),
            'statusId' => $subject->getStatusId(),
            'statusTitle' => $subject->getStatusTitle(),
            'errors' => $subject->getErrors(),
        ];
    }

    /**
     * Includes data
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeData(Model $subject): ResourceInterface
    {
        return $this->primitive($subject->getData());
    }

    /**
     * Includes form data
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeFormData(Model $subject): ResourceInterface
    {
        return $this->primitive($subject->getFormData());
    }

    /**
     * Includes entry id
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeEntryId(Model $subject): ResourceInterface
    {
        return $this->primitive($subject->getEntryId());
    }

    /**
     * Includes task
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeTask(Model $subject): ResourceInterface
    {
        return $this->item($subject->getTask(), TaskTransformer::class);
    }
}