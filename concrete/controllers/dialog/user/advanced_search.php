<?php
namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\ManagerFactory;

class AdvancedSearch extends AdvancedSearchController
{

    protected $supportsSavedSearch = false;

    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');
        return $dh->canAccessUserSearchInterface();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\User\Search\SearchProvider');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('user');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return \URL::to('/ccm/system/search/users/preset', $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return \URL::to('/ccm/system/search/users/current');
    }

    public function getBasicSearchBaseURL()
    {
        return \URL::to('/ccm/system/search/users/basic');
    }

}
