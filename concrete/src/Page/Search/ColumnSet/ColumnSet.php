<?php
namespace Concrete\Core\Page\Search\ColumnSet;

use Concrete\Core\Page\Search\SearchProvider;
use Concrete\Core\Search\Column\CollectionAttributeKeyColumn;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Support\Facade\Facade;
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
