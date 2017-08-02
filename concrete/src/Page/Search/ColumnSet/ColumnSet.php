<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Search\Column\CollectionAttributeKeyColumn;
use Concrete\Core\Search\Column\Set;
use User;

class ColumnSet extends Set
{
    protected $attributeClass = 'CollectionAttributeKey';

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        $col = new CollectionAttributeKeyColumn($ak);
        return $col;
    }

    public static function getCurrent()
    {
        $u = new User();
        $fldc = $u->config('PAGE_LIST_DEFAULT_COLUMNS');
        if ($fldc != '') {
            $fldc = @unserialize($fldc);
        }
        if (!($fldc instanceof Set)) {
            $fldc = new DefaultSet();
        }

        return $fldc;
    }
}
