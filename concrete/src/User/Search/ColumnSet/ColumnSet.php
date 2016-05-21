<?php
namespace Concrete\Core\User\Search\ColumnSet;

use PermissionKey;
use User;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Column\AttributeKeyColumn;

class ColumnSet extends Set
{
    protected $attributeClass = 'UserAttributeKey';

    public function getColumns()
    {
        $columns = array();
        $pk = PermissionKey::getByHandle('view_user_attributes');
        foreach ($this->columns as $col) {
            if ($col instanceof AttributeKeyColumn) {
                $uk = $col->getAttributeKey();
                if ($pk->validate($uk)) {
                    $columns[] = $col;
                }
            } else {
                $columns[] = $col;
            }
        }

        return $columns;
    }

    public static function getCurrent()
    {
        $u = new User();
        $fldc = $u->config('USER_LIST_DEFAULT_COLUMNS');
        if ($fldc != '') {
            $fldc = @unserialize($fldc);
        }
        if (!($fldc instanceof Set)) {
            $fldc = new DefaultSet();
        }

        return $fldc;
    }
}
