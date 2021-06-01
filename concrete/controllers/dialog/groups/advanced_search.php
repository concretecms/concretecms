<?php /** @noinspection PhpUnused */

namespace Concrete\Controller\Dialog\Groups;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManager;

class AdvancedSearch extends AdvancedSearchController
{
    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/user');

        return $dh->canAccessUserSearchInterface();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\User\Group\Search\SearchProvider');
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedGroupSearch::class)->findAll();
        }
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/users/groups', 'advanced_search');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('group');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/dashboard/users/groups', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/groups/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/groups/advanced_search/preset/edit?presetID=' . $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return (string)Url::to('/ccm/system/search/groups/current');
    }

    public function getBasicSearchBaseURL()
    {
        return (string)Url::to('/ccm/system/search/groups/basic');
    }
}
