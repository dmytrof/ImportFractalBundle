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

use Dmytrof\ImportBundle\Importer\AbstractImporter;
use Dmytrof\ImportBundle\Model\{ImportableField, ImportableFields};

class TestImporter extends AbstractImporter
{
    public const CODE = 'test';
    public const TITLE = 'Test importer';

    public function getImportableFields(): ImportableFields
    {
        return new ImportableFields([
            new ImportableField('title', 'Title'),
            new ImportableField('price', 'Price'),
        ]);
    }
}
