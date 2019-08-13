<?php
namespace Concrete\Core\Attribute\Key\ImportLoader;

use Concrete\Core\Entity\Attribute\Key\Key;

/**
 * @since 8.0.0
 */
interface ImportLoaderInterface
{
    public function load(Key $key, \SimpleXMLElement $element);
}
