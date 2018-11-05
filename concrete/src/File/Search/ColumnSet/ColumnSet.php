<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\Search\Column\FileAttributeKeyColumn;
use Concrete\Core\Support\Facade\Facade;
use User;
use Concrete\Core\File\Search\SearchProvider;
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
