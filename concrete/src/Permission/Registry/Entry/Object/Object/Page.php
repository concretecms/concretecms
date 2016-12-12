<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

class Page implements ObjectInterface
{

    protected $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function getPermissionObject()
    {
        if (is_object($this->page)) {
            return $this->page;
        }

        $page = \Concrete\Core\Page\Page::getByPath($this->page);
        if (is_object($page) && !$page->isError()) {
            return $page;
        }
    }


}
