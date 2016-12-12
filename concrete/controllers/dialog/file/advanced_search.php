<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\ManagerFactory;
use FilePermissions;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;

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
        return \URL::to('/ccm/system/search/files/preset', $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return \URL::to('/ccm/system/search/files/current');
    }

    public function getBasicSearchBaseURL()
    {
        return \URL::to('/ccm/system/search/files/basic');
    }


}
