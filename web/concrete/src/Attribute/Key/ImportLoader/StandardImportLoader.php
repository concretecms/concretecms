<?php

namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\AttributeKey\AttributeKey;

class StandardImporterLoader implements ImportLoaderInterface
{

    public function load(AttributeKey $key, \SimpleXMLElement $element)
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
