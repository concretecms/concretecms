<?php /** @noinspection PhpUnused */

namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Entity\Search\SavedUserSearch;
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
        return $this->app->make('Concrete\Core\User\Search\SearchProvider');
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedUserSearch::class)->findAll();
        }
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/users/search', 'advanced_search');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('user');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/dashboard/users/search', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/user/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/user/advanced_search/preset/edit?presetID=' . $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return (string)Url::to('/ccm/system/search/users/current');
    }

    public function getBasicSearchBaseURL()
    {
        return (string)Url::to('/ccm/system/search/users/basic');
    }
}
