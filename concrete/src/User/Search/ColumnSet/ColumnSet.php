<?php
namespace Concrete\Core\User\Search\ColumnSet;

use Concrete\Core\Search\Column\UserAttributeKeyColumn;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Search\SearchProvider;
use PermissionKey;
use User;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Column\AttributeKeyColumn;

class ColumnSet extends Set
{
    protected $attributeClass = 'UserAttributeKey';

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = call_user_func(array($this->attributeClass, 'getByHandle'), $akHandle);
        $col = new UserAttributeKeyColumn($ak);
        return $col;
    }

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

        $app = Facade::getFacadeApplication();
        /**
         * @var $provider SearchProvider
         */
        $provider = $app->make(SearchProvider::class);
        $query = $provider->getSessionCurrentQuery();
        if ($query) {
            $columns = $query->getColumns();
            return $columns;
        }

        return new DefaultSet();
    }
}
