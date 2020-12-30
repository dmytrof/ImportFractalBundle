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

use Dmytrof\ImportBundle\Reader\ReaderInterface as Model;
use Dmytrof\FractalBundle\Transformer\AbstractTransformer;
use League\Fractal\Resource\ResourceInterface;

class ReaderTransformer extends AbstractTransformer
{
    protected const SUBJECT_CLASS = Model::class;

    protected $availableIncludes = [
        'options',
    ];

    /**
     * Transforms reader to array
     * @param Model $subject
     * @return array
     */
    public function transformSubject($subject): array
    {
        return [
            'title' => $subject->getTitle(),
            'code' => $subject->getCode(),
        ];
    }

    /**
     * Includes options
     * @param Model $subject
     * @return ResourceInterface
     */
    public function includeOptions(Model $subject): ResourceInterface
    {
        return $this->primitive($subject->getOptions() ? $subject->getOptions()->toArray() : null);
    }
}