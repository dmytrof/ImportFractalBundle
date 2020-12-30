DmytrofImportFractalBundle
====================

This bundle helps you to implement [Fractal by League](https://fractal.thephpleague.com) for 
[DmytrofImportBundle](https://github.com/dmytrof/ImportBundle)

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
        
        