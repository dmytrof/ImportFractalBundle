<?php

/*
 * This file is part of the DmytrofImportFractalBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ImportFractalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\{Builder\TreeBuilder, ConfigurationInterface};

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dmytrof_import_fractal');

        return $treeBuilder;
    }
}
