<?php
namespace Concrete\Controller\Dialog\Page;

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
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');

        return $dh->canRead();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\Page\Search\SearchProvider');
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedPageSearch');
        }

        return null;
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

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string) (string) URL::to('/ccm/system/dialogs/page/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) (string) URL::to('/ccm/system/dialogs/page/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
