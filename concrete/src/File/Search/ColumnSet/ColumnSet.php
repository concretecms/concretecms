<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\Search\Column\FileAttributeKeyColumn;
use Concrete\Core\Search\Column\Set as DatabaseItemListColumnSet;

class ColumnSet extends DatabaseItemListColumnSet
{
    protected $attributeClass = 'FileAttributeKey';

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        $col = new FileAttributeKeyColumn($ak);
        return $col;
    }
}
