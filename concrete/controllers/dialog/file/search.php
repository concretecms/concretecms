<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Search\FileFolder;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Result\ResultFactory;
use FilePermissions;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/file/search';

    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles() || $cp->canAddFile()) {
            return true;
        } else {
            return false;
        }
    }

    public function view()
    {
        $provider = $this->app->make(SearchProvider::class);
        $queryFactory = $this->app->make(QueryFactory::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $query = $queryFactory->createQuery($provider);
        $result = $resultFactory->createFromQuery($provider, $query);
        $this->set('result', $result);
    }
}
