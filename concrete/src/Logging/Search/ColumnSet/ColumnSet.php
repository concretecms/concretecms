<?php

namespace Concrete\Core\Logging\Search\ColumnSet;

use Concrete\Core\Logging\Search\SearchProvider;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Support\Facade\Facade;

class ColumnSet extends Set
{
    protected $attributeClass = 'CollectionAttributeKey';

    public static function getCurrent()
    {
        $app = Facade::getFacadeApplication();
        /** @var $provider SearchProvider */
        $provider = $app->make(SearchProvider::class);
        $query = $provider->getSessionCurrentQuery();

        if ($query) {
            return $query->getColumns();
        }

        return $provider->getDefaultColumnSet();
    }
}
