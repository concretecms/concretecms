<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\ManagerFactory;
use Doctrine\ORM\EntityManager;
use URL;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;

    protected function canAccess()
    {
        $page = Page::getByPath('/dashboard/calendar/events');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function getSearchProvider()
    {
        return false; // not implemented yet
    }

    public function getSearchPresets()
    {
        return false; // not implemented yet
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('calendar_event');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return false; // not implemented yet
    }

    public function getCurrentSearchBaseURL()
    {
        return false; // not implemented yet
    }

    public function getBasicSearchBaseURL()
    {
        return false; // not implemented yet
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return false; // not implemented yet
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return false; // not implemented yet
    }
}
