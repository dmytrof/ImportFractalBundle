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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Dmytrof\ImportFractalBundle\Transformer\{ImporterTransformer, ItemTransformer, ReaderTransformer, TaskTransformer};

return static function (ContainerConfigurator $container): void {
    // Transformers are tagged by autoconfiguration of DmytrofFractalBundle
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->set(ImporterTransformer::class);
    $services->set(ReaderTransformer::class);
    $services->set(TaskTransformer::class);
    $services->set(ItemTransformer::class);
};
