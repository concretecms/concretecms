<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
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

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedFileSearch');
        }

        return null;
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('file');
    }

    public function getRequestDefaultSort()
    {
        return $this->request->request->get('fSearchDefaultSort');
    }

    public function getRequestDefaultSortDirection()
    {
        return $this->request->request->get('fSearchDefaultSortDirection');
    }

    public function onAfterSavePreset(SavedSearch $search)
    {
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        \Concrete\Core\Tree\Node\Type\SearchPreset::addSearchPreset($search, $folder);
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/search/files/preset', $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return (string) URL::to('/ccm/system/search/files/current');
    }

    public function getBasicSearchBaseURL()
    {
        return (string) URL::to('/ccm/system/search/files/basic');
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
