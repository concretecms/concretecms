<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;
use League\Url\Url;

class AdvancedSearch extends AdvancedSearchController
{

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var string
     */
    protected $pagePath = '/dashboard/express/entries';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        parent::__construct();
    }

    protected function loadEntity()
    {
        if (!isset($this->entity)) {
            $entity = $this->objectManager->getObjectByID($this->request->query->get('exEntityID'));
            if (is_object($entity)) {
                $this->entity = $entity;
            } else {
                throw new \Exception(t('Access Denied.'));
            }
        }
        if ($this->request->query->has('cID')) {
            $page = Page::getByID($this->request->query->get('cID'));
            if ($page && !$page->isError()) {
                $this->pagePath = $page->getCollectionPath();
            }
        }
    }

    protected function canAccess()
    {
        $this->loadEntity();
        $ep = new Checker($this->entity);
        return $ep->canViewExpressEntries();
    }

    public function view()
    {
        $this->loadEntity();
        parent::view();
    }

    protected function getAttributeCategory()
    {
        $this->loadEntity();
        $category = $this->app->make(ExpressCategory::class, ['entity' => $this->entity]);
        return $category;
    }

    public function getSearchProvider()
    {
        $provider = $this->app->make(SearchProvider::class, [
            'category' => $this->getAttributeCategory(),
            'entity' => $this->entity
        ]);
        return $provider;
    }

    public function getSearchPresets()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedExpressSearch::class)->findByEntity($this->entity);
        }

        return null;
    }

    public function getSubmitMethod()
    {
        return 'get';
    }

    public function getSubmitAction()
    {
        return $this->app->make('url')->to($this->pagePath, 'advanced_search', $this->entity->getId());
    }

    public function getFieldManager()
    {
        $this->loadEntity();
        $provider = $this->getSearchProvider();
        return $provider->getFieldManager();
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return $this->app->make('url')->to($this->pagePath, 'preset', $search->getId());
    }

    public function getAddFieldAction()
    {
        $action = $this->action('add_field');
        $url = Url::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }

    public function getSavePresetAction()
    {
        $action = parent::getSavePresetAction();
        $url = Url::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/ccm/system/dialogs/express/advanced_search/preset/delete?presetID=' . $search->getID() . '&exEntityID=' . $this->entity->getId());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return $this->app->make('url')->to('/ccm/system/dialogs/express/advanced_search/preset/edit?presetID=' . $search->getID() . '&exEntityID=' . $this->entity->getId());
    }
}
