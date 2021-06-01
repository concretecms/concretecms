<?php
namespace Concrete\Core\User\Group\Search;

use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\User\Group\FolderItemList;
use Concrete\Core\User\Group\Search\ColumnSet\Available;
use Concrete\Core\User\Group\Search\ColumnSet\ColumnSet;
use Concrete\Core\User\Group\Search\ColumnSet\DefaultSet;
use Concrete\Core\User\Group\Search\Result\Result;

class SearchProvider extends AbstractSearchProvider
{

    public function getFieldManager()
    {
        return ManagerFactory::get('group');
    }

    public function getSessionNamespace()
    {
        return 'group';
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

    public function getItemList()
    {
        $list = new FolderItemList();
        return $list;
    }

    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }

    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }

    public function getSavedSearch()
    {
        return new SavedGroupSearch();
    }


}
