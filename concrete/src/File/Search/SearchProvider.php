<?php
namespace Concrete\Core\File\Search;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\File\Search\ColumnSet\ColumnSet;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\QueryableInterface;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider implements QueryableInterface
{
    /**
     * @var \Concrete\Core\Attribute\Category\FileCategory
     */
    protected $category;

    /**
     * @return string
     */
    public function getSessionNamespace()
    {
        return 'file';
    }

    public function __construct(FileCategory $category, Session $session)
    {
        $this->category = $category;
        parent::__construct($session);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getCustomAttributeKeys()
     */
    public function getCustomAttributeKeys()
    {
        return $this->category->getSearchableList();
    }

    /**
     * @return \Concrete\Core\File\Search\ColumnSet\Available
     */
    public function getAvailableColumnSet()
    {
        return new Available();
    }

    /**
     * @return string
     */
    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }

    /**
     * @return \Concrete\Core\File\Search\ColumnSet\ColumnSet
     */
    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }

    /**
     * @return \Concrete\Core\File\Search\ColumnSet\DefaultSet
     */
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }

    /**
     * @return \Concrete\Core\File\FileList
     */
    public function getItemList()
    {
        $list = new FileList();
        $list->setPermissionsChecker(function ($file) {
            $fp = new \Permissions($file);

            return $fp->canViewFileInFileManager();
        });

        return $list;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\AbstractSearchProvider::getItemsPerPage()
     */
    public function getItemsPerPage()
    {
        $searchRequest = new StickyRequest('file_manager_folder');
        $searchParams = $searchRequest->getSearchRequest();
        $node = empty($searchParams['folder']) ? null : Node::getByID($searchParams['folder']);

        if (($node instanceof SearchPreset)) {
            $searchObj = $node->getSavedSearchObject();

            return $searchObj->getQuery()->getItemsPerPage();
        }

        $sessionQuery = $this->getSessionCurrentQuery();
        if ($sessionQuery instanceof \Concrete\Core\Entity\Search\Query) {
            return (int) $sessionQuery->getItemsPerPage();
        }

        $itemsPerPageSession = $this->getItemsPerPageSession();
        if (!empty($itemsPerPageSession)) {
            return $itemsPerPageSession;
        }

        $app = Application::getFacadeApplication();
        $config = $app->make('config');

        return $config->get('concrete.file_manager.results');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\AbstractSearchProvider::getItemsPerPageOptions()
     */
    public function getItemsPerPageOptions()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $options = $config->get('concrete.file_manager.items_per_page_options');
        if (!empty($options)) {
            return $options;
        }

        return parent::getItemsPerPageOptions();
    }

    /**
     * Set items per page option in session.
     *
     * @param int $itemsPerPage
     */
    public function setItemsPerPageSession($itemsPerPage)
    {
        $this->session->set('search/' . $this->getSessionNamespace() . '/items_per_page', $itemsPerPage);
    }

    /**
     * Retrieve the items per page option from the session.
     *
     * @return int|null
     */
    public function getItemsPerPageSession()
    {
        $variable = 'search/' . $this->getSessionNamespace() . '/items_per_page';
        if ($this->session->has($variable)) {
            return (int) $this->session->get($variable);
        }

        return null;
    }

    /**
     * Clear the item per page option from the session.
     */
    public function clearItemsPerPageSession()
    {
        $this->session->remove('search/' . $this->getSessionNamespace() . '/items_per_page');
    }

    /**
     * @return \Concrete\Core\File\Search\Result\Result
     */
    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }

    /**
     * @return \Concrete\Core\Entity\Search\SavedFileSearch
     */
    public function getSavedSearch()
    {
        return new SavedFileSearch();
    }
}
