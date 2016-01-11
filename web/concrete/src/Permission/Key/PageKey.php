<?php
namespace Concrete\Core\Permission\Key;

class PageKey extends Key
{
    protected $multiplePageArray; // bulk operations
    public function setMultiplePageArray($pages)
    {
        $this->multiplePageArray = $pages;
    }
    public function getMultiplePageArray()
    {
        return $this->multiplePageArray;
    }
}
