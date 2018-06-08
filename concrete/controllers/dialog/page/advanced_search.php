<?php
namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\ManagerFactory;
use URL;

class AdvancedSearch extends AdvancedSearchController
{

    protected $supportsSavedSearch = false;

    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');

        return $dh->canRead();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\Page\Search\SearchProvider');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('page');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/search/pages/preset', $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return (string) URL::to('/ccm/system/search/pages/current');
    }

    public function getBasicSearchBaseURL()
    {
        return (string) URL::to('/ccm/system/search/pages/basic');
    }

}
