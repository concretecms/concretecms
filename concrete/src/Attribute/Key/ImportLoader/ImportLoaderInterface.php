<?php
namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\Attribute\Key\Key;

interface ImportLoaderInterface
{
    public function load(Key $key, \SimpleXMLElement $element);
}
