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

use Symfony\Contracts\Translation\{TranslatorInterface, TranslatorTrait};

/**
 * Translator returning message ids (with parameters replaced)
 */
final class IdentityTranslator implements TranslatorInterface
{
    use TranslatorTrait;
}
