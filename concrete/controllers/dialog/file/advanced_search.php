<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Search\Field\ManagerFactory;
use Doctrine\ORM\EntityManager;
use FilePermissions;
use URL;

class AdvancedSearch extends AdvancedSearchController
{
    protected function canAccess()
    {
        $cp = FilePermissions::getGlobal();
        if ($cp->canSearchFiles()) {
            return true;
        } else {
            return false;
        }
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\File\Search\SearchProvider');
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedFileSearch::class)->findAll();
        }

        return null;
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/files/search', 'advanced_search');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('file');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/dashboard/files/search', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/dialogs/file/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/dialogs/file/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
