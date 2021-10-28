<?php /** @noinspection PhpUnused */

namespace Concrete\Controller\Dialog\Logs;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedFileSearch;
use Concrete\Core\Entity\Search\SavedLogSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class AdvancedSearch extends AdvancedSearchController
{
    protected function canAccess()
    {
        $taskPermission = Key::getByHandle("view_log_entries");
        if (is_object($taskPermission)) {
            return $taskPermission->validate();
        } else {
            // This is a previous concrete5 versions that don't have the new task permission installed
            $app = Application::getFacadeApplication();
            $u = $app->make(User::class);
            return $u->isRegistered();
        }
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\Logging\Search\SearchProvider');
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedLogSearch::class)->findAll();
        }

        return null;
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/reports/logs', 'advanced_search');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('logging');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/dashboard/reports/logs', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/logs/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/logs/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
