<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\ResultFactory;
use Symfony\Component\HttpFoundation\Request;

class Search extends DashboardPageController
{

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
     * @param Query $query
     * @void
     */
    protected function renderSearchQuery(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        $headerMenu = $this->app->make(ElementManager::class)->get('files/search/menu');
        $headerSearch = $this->app->make(ElementManager::class)->get('files/search/search');

        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        $result = $resultFactory->createFromQuery($provider, $query);
        $headerMenu->getElementController()->setQuery($query);
        $headerSearch->getElementController()->setQuery($query);

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);
        $this->setThemeViewTemplate('full.php');
    }

    public function view()
    {
        $query = $this->getQueryFactory()->createDefaultQuery(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET, [
            new KeywordsField()
        ]);
        $this->renderSearchQuery($query);
    }

    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );
        $this->renderSearchQuery($query);
    }

    public function search_preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedFileSearch::class, $presetID);
            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $this->renderSearchQuery($query);
                return;
            }
        }
        $this->view();
    }

}
