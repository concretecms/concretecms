<?php
namespace Concrete\Core\Logging\Search;

use Concrete\Core\Logging\LogList;
use Concrete\Core\Logging\Search\ColumnSet\DefaultSet;
use Concrete\Core\Logging\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Logging\Search\ColumnSet\Available;
use Concrete\Core\Logging\Search\ColumnSet\ColumnSet;
use Concrete\Core\Entity\Search\SavedLogSearch;

class SearchProvider extends AbstractSearchProvider
{

    protected $category;

    public function getFieldManager()
    {
        return ManagerFactory::get('logging');
    }

    public function getSessionNamespace()
    {
        return 'logging';
    }

    public function getCustomAttributeKeys()
    {
        return [];
    }

    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }

    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }

    public function getItemList()
    {
        return new LogList();
    }

    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }

    public function getSavedSearch()
    {
        return new SavedLogSearch();
    }

}
