<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\ResultFactory;
use Symfony\Component\HttpFoundation\Request;

class Search extends DashboardPageController
{

    public function view()
    {
        $provider = $this->app->make(SearchProvider::class);
        $headerMenu = $this->app->make(ElementManager::class)->get('files/search/menu');
        $headerSearch = $this->app->make(ElementManager::class)->get('files/search/search');

        $filesystem = $this->app->make(Filesystem::class);
        $folder = $filesystem->getRootFolder();
        $list = new FolderItemList();
        $list->filterByParentFolder($folder);

        if ($this->request->query->has('keywords')) {
            $list->filterByKeywords($this->request->query->get('keywords'));
        }

        $set = new DefaultSet();
        $result = new Result($set, $list);

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);
        $this->setThemeViewTemplate('full.php');
    }

    public function advanced_search()
    {
        $provider = $this->app->make(SearchProvider::class);

        $queryFactory = $this->app->make(QueryFactory::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $query = $queryFactory->createFromAdvancedSearchRequest($provider, $this->request, Request::METHOD_GET);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        $result = $resultFactory->createFromQuery($provider, $query);

        $headerMenu = $this->app->make(ElementManager::class)->get('files/search/menu');
        $headerSearch = $this->app->make(ElementManager::class)->get('files/search/search');
        $headerMenu->getElementController()->setQuery($query);
        $headerSearch->getElementController()->setQuery($query);

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);
        $this->setThemeViewTemplate('full.php');

    }
}
