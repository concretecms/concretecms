<?php
namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\ManagerFactory;
use Doctrine\ORM\EntityManager;
use URL;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;

    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');

        return $dh->canAccessUserSearchInterface();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\User\Search\SearchProvider');
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedUserSearch');
        }

        return null;
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('user');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/search/users/preset', $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return (string) URL::to('/ccm/system/search/users/current');
    }

    public function getBasicSearchBaseURL()
    {
        return (string) URL::to('/ccm/system/search/users/basic');
    }

    public function getSavedSearchDeleteURL(SavedSearch $search, $confirm = false)
    {
        return (string) URL::to('/ccm/system/dialogs/user/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/dialogs/user/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
