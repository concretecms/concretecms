<?php
namespace Concrete\Core\File\Search;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\File\Search\ColumnSet\ColumnSet;
use Concrete\Core\Search\QueryableInterface;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\Session\Session;
use Concrete\Core\Entity\Search\SavedFileSearch;

class SearchProvider extends AbstractSearchProvider implements QueryableInterface
{

    protected $category;

    public function getSessionNamespace()
    {
        return 'file';
    }

    public function __construct(FileCategory $category, Session $session)
    {
        $this->category = $category;
        parent::__construct($session);
    }

    public function getCustomAttributeKeys()
    {
        return $this->category->getSearchableList();
    }

    public function getAvailableColumnSet()
    {
        return new Available();
    }

    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }

    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }

    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }

    public function getItemList()
    {
        $list = new FileList();
        $list->setPermissionsChecker(function ($file) {
            $fp = new \Permissions($file);
            return $fp->canViewFileInFileManager();
        });
        return $list;
    }

    public function getItemsPerPage()
    {
        $searchRequest = new StickyRequest('file_manager_folder');
        $searchParams = $searchRequest->getSearchRequest();
        $node = empty($searchParams['folder']) ? null : Node::getByID($searchParams['folder']);

        if (!($node instanceof SearchPreset)) {
            return parent::getItemsPerPage();
        }

        $searchObj = $node->getSavedSearchObject();
        return $searchObj->getQuery()->getItemsPerPage();
    }


    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }

    public function getSavedSearch()
    {
        return new SavedFileSearch();
    }
    
}
