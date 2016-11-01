<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;

interface AttributeKeyHandleGeneratorInterface
{
    public function generate(Key $key);
}
