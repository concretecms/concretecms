<?php
namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\Attribute\Key\Key;

class StandardImportLoader implements ImportLoaderInterface
{
    public function load(Key $key, \SimpleXMLElement $element)
    {
        $key->setAttributeKeyName((string) $element['name']);
        $key->setAttributeKeyHandle((string) $element['handle']);
        $indexed = (string) $element['indexed'];
        $searchable = (string) $element['searchable'];
        $internal = (string) $element['internal'];
        if ($indexed === '1') {
            $key->setIsAttributeKeyContentIndexed(true);
        }
        if ($indexed === '1') {
            $key->setIsAttributeKeyContentIndexed(true);
        }

        if ($internal === '1') {
            $key->setIsAttributeKeyInternal(true);
        }

        return $key;
    }
}
