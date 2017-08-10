<?php
namespace Concrete\Core\Page\Search;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\ColumnSet\DefaultSet;
use Concrete\Core\Page\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Page\Search\ColumnSet\Available;
use Concrete\Core\Page\Search\ColumnSet\ColumnSet;
use Symfony\Component\HttpFoundation\Session\Session;
use Concrete\Core\Entity\Search\SavedPageSearch;

class SearchProvider extends AbstractSearchProvider
{

    protected $category;

    public function getSessionNamespace()
    {
        return 'page';
    }


    public function __construct(PageCategory $category, Session $session)
    {
        $this->category = $category;
        parent::__construct($session);
    }

    public function getCustomAttributeKeys()
    {
        return $this->category->getSearchableList();
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
        $site = \Core::make('site')->getActiveSiteForEditing();
        $list = new PageList();
        $list->setSiteTreeObject($site);
        return $list;
    }

    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    function getSavedSearch()
    {
        return new SavedPageSearch();
    }

}
