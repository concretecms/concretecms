<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedFileSearch;
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
     * @param Query $query
     * @void
     */
    protected function renderSearchQuery(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);

        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();

        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        $result = $resultFactory->createFromQuery($provider, $query);
        $headerMenu->getElementController()->setQuery($query);
        $headerSearch->getElementController()->setQuery($query);

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);
        $this->setThemeViewTemplate('full.php');
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;
        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }
        return new KeywordsField($keywords);
    }

    public function view()
    {
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [$this->getSearchKeywordsField()]);
        $this->renderSearchQuery($query);
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
                $this->renderSearchQuery($query);

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
                        new FolderField($folder),
                        $this->getSearchKeywordsField(),
                    ]
                );
                $this->renderSearchQuery($query);

                $factory = $this->createBreadcrumbFactory();
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $folder));
                $this->headerSearch->getElementController()->setHeaderSearchAction(
                    $this->app->make('url')->to('/dashboard/files/search', 'folder', $folder->getTreeNodeID())
                );
                $this->headerMenu->getElementController()->setCurrentFolder($folder);
                return;
            }
        }
        $this->view();
    }


}
