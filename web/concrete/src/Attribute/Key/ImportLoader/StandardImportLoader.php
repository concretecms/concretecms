<?php

namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\Attribute\Key\Key;

class StandardImporterLoader implements ImportLoaderInterface
{

    public function load(Key $key, \SimpleXMLElement $element)
    {
        $key->setAttributeKeyName((string) $element['name']);
        $key->setAttributeKeyHandle((string) $element['handle']);
        $key->setIsAttributeKeyContentIndexed((bool) $element['indexed']);
        $key->setIsAttributeKeySearchable((bool) $element['searchable']);
        $controller = $key->getController();
        $controller->setAttributeKey($key);
        $controller->importKey($element);
        return $controller->getAttributeKey();
    }

}
