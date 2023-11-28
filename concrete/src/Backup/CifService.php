<?php

declare(strict_types=1);

namespace Concrete\Core\Backup;

use SimpleXMLElement;

class CifService
{
    public static function getBool(?SimpleXMLElement $elementOrAttribute, bool $default = false): bool
    {
        if ($elementOrAttribute === null || $elementOrAttribute->getName() === '') {
            return $default;
        }

        return filter_var((string) $elementOrAttribute, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}
