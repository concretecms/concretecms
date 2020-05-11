<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\Field\Field\FolderField;
use Concrete\Core\File\Search\Menu\MenuFactory;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardFileManagerBreadcrumbFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Symfony\Component\HttpFoundation\Request;

class Search extends DashboardPageController
{

    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    /**
     * @return DashboardFileManagerBreadcrumbFactory
     */
    protected function createBreadcrumbFactory()
    {
        return $this->app->make(DashboardFileManagerBreadcrumbFactory::class);
    }

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('files/search/menu');
        }
        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('files/search/search');
        }
        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        $headerMenu->getElementController()->setQuery($result->getQuery());
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);
        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;
        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }
        return new KeywordsField($keywords);
    }

    protected function getSearchFolderField(FileFolder $folder)
    {
        // This method is called in two spots: 1. the basic search, 2. when you browser to a sub folder.
        // In both cases, if keywords are present to further filter the search, we want to not only search
        // the folder you're specifying, but ALSO the sub-folders.
        // Note, this is separate from any folders specified within advanced search.
        if ($this->request->query->has('keywords')) {
            $field = new FolderField($folder, true);
        } else {
            $field = new FolderField($folder);
        }
        return $field;
    }

    protected function getRootFolder()
    {
        $filesystem = $this->app->make(Filesystem::class);
        $rootFolder = $filesystem->getRootFolder();
        return $rootFolder;
    }

    public function view()
    {
        $rootFolder = $this->getRootFolder();
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField(),
            $this->getSearchFolderField($rootFolder)
        ]);
        $result = $this->createSearchResult($query);
        $this->renderSearchResult($result);
        $this->setCurrentFolder($rootFolder);
    }

    /**
     * Responsible for setting the current folder in the header menu, and in the JavaScript that powers the search table.
     *
     * @param FileFolder $folder
     */
    protected function setCurrentFolder(FileFolder $folder)
    {
        $this->set('folderID', $folder->getTreeNodeID());
        $this->headerMenu->getElementController()->setCurrentFolder($folder);
    }

    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );
        $this->renderSearchQuery($query);
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedFileSearch::class, $presetID);
            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                $factory = $this->createBreadcrumbFactory();
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $preset));

                return;
            }
        }
        $this->view();
    }

    public function folder($folderID = null)
    {
        if ($folderID) {
            $folder = Node::getByID($folderID);
            if ($folder && $folder instanceof FileFolder) {
                $query = $this->getQueryFactory()->createQuery(
                    $this->getSearchProvider(), [
                        $this->getSearchKeywordsField(),
                        $this->getSearchFolderField($folder),
                    ]
                );
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                $factory = $this->createBreadcrumbFactory();
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $folder));
                $this->headerSearch->getElementController()->setHeaderSearchAction(
                    $this->app->make('url')->to('/dashboard/files/search', 'folder', $folder->getTreeNodeID())
                );
                $this->setCurrentFolder($folder);
                return;
            }
        }
        $this->view();
    }


}
