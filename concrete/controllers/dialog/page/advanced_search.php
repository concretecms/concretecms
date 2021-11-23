<?php /** @noinspection PhpUnused */

namespace Concrete\Controller\Dialog\Page;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Entity\Search\SavedPageSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Entity\Search\SavedUserSearch;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManager;

class AdvancedSearch extends AdvancedSearchController
{
    protected function canAccess()
    {
        $dh = $this->app->make('helper/concrete/dashboard/sitemap');
        return $dh->canRead();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\Page\Search\SearchProvider');
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedPageSearch::class)->findAll();
        }
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to('/dashboard/sitemap/search', 'advanced_search');
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('page');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/dashboard/sitemap/search', 'preset', $search->getID());
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/page/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string)Url::to('/ccm/system/dialogs/page/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
