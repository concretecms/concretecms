<?php
namespace Concrete\Core\User\Group\Search\ColumnSet;

use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Search\SearchProvider;
use Concrete\Core\Search\Column\Set;

class ColumnSet extends Set
{
    protected $attributeClass = 'CollectionAttributeKey';

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
