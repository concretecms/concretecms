<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\Search\Column\FileAttributeKeyColumn;
use User;
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

    public static function getCurrent()
    {
        $u = new User();
        $fldc = $u->config('FILE_LIST_DEFAULT_COLUMNS');
        if ($fldc != '') {
            $fldc = @unserialize($fldc);
        }
        if (!($fldc instanceof DatabaseItemListColumnSet)) {
            $fldc = new DefaultSet();
        }

        return $fldc;
    }
}
