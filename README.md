DmytrofImportFractalBundle
====================

This bundle helps you to implement [Fractal by League](https://fractal.thephpleague.com) for 
[DmytrofImportBundle](https://github.com/dmytrof/ImportBundle)

## Requirements

* PHP 8.4 or higher
* Symfony 6.4, 7.x or 8.x
* dmytrof/import-bundle 1.24+, dmytrof/fractal-bundle 1.4+, dmytrof/models-management-fractal-bundle 1.4+

## Installation

### Step 1: Install the bundle

    $ composer require dmytrof/import-fractal-bundle 
    
### Step 2: Enable the bundle

    <?php
        // config/bundles.php
        
        return [
            // ...
            Dmytrof\ImportFractalBundle\DmytrofImportFractalBundle::class => ['all' => true],
        ];

The bundle registers Fractal transformers for ImportBundle models:

* `Dmytrof\ImportFractalBundle\Transformer\TaskTransformer` (includes: `importStatistics`, `link`, `paginationData`, `importer`, `reader`)
* `Dmytrof\ImportFractalBundle\Transformer\ItemTransformer` (includes: `data`, `formData`, `task`, `entryId`)
* `Dmytrof\ImportFractalBundle\Transformer\ImporterTransformer` (includes: `options`)
* `Dmytrof\ImportFractalBundle\Transformer\ReaderTransformer` (includes: `options`)

Usage:

    use Dmytrof\FractalBundle\Service\FractalManager;
    use Dmytrof\ImportFractalBundle\Transformer\TaskTransformer;

    $data = $fractalManager->parseIncludes('importer,reader')->getModelData($task, TaskTransformer::class);

## Tests

    $ composer install
    $ vendor/bin/phpunit
