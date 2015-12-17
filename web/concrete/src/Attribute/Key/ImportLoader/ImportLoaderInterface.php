<?php

namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\AttributeKey\AttributeKey;

interface ImportLoaderInterface
{

    public function load(AttributeKey $key, \SimpleXMLElement $element);

}
