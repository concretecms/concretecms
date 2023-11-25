<?php

declare(strict_types=1);

namespace Concrete\Core\Backup\ContentImporter;

use SimpleXMLElement;

trait ImportFromCifTrait
{
    protected static function getBoolFromCif(?SimpleXMLElement $elementOrAttribute): bool
    {
        return $elementOrAttribute === null ? false : filter_var((string) $elementOrAttribute, FILTER_VALIDATE_BOOLEAN);
    }
}
