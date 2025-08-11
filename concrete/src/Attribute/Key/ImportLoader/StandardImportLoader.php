<?php
namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Utility\Service\Xml;

class StandardImportLoader implements ImportLoaderInterface
{
    public function load(Key $key, \SimpleXMLElement $element)
    {
        $xml = app(Xml::class);
        $key->setAttributeKeyName((string) $element['name']);
        $key->setAttributeKeyHandle((string) $element['handle']);
        $key->setIsAttributeKeyContentIndexed($xml->getBool($element['indexed']));
        $key->setIsAttributeKeySearchable($xml->getBool($element['searchable']));
        $key->setIsAttributeKeyInternal($xml->getBool($element['internal']));

        return $key;
    }
}
