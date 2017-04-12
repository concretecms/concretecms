<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;
use Concrete\Core\Page\Type\Type;

class PageType implements ObjectInterface
{
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getPermissionObject()
    {
        if (is_object($this->type)) {
            return $this->type;
        }

        $type = Type::getByHandle($this->type);
        if (is_object($type)) {
            return $type;
        }
    }


}
