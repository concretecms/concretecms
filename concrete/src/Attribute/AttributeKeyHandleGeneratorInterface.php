<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;

/**
 * @since 8.0.0
 */
interface AttributeKeyHandleGeneratorInterface
{
    public function generate(Key $key);
}
