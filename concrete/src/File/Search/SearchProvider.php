<?php
namespace Concrete\Core\File\Search;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\File\Search\ColumnSet\Available;
use Concrete\Core\File\Search\ColumnSet\ColumnSet;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\SearchPreset;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{

    public function getFieldManager()
    {
        return ManagerFactory::get('file');
    }

    /**
     * @var \Concrete\Core\Attribute\Category\FileCategory
     */
    protected $category;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @return string
     */
    public function getSessionNamespace()
    {
        return 'file';
    }

    public function __construct(Filesystem $filesystem, FileCategory $category, Session $session)
    {
        $this->category = $category;
        $this->filesystem = $filesystem;
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
        $list = new FolderItemList();
        $list->setupAutomaticSorting();
        return $list;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\AbstractSearchProvider::getItemsPerPage()
     */
    public function getItemsPerPage()
    {
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
