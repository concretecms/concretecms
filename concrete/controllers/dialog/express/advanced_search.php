<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Controller\Element\Search\Express\CustomizeResults;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Express\Search\Field\Manager;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Doctrine\ORM\EntityManager;
use League\Url\Url as LeagueUrl;
use URL;
use Permissions;
use Exception;

class AdvancedSearch extends AdvancedSearchController
{
    /**
     * @var Express
     */
    protected $entity;

    /**
     * @var string
     */
    protected $entityID;

    protected $supportsSavedSearch = true;

    public function view()
    {
        $this->loadEntity();
        parent::view();
    }

    public function setEntityID($entityID)
    {
        $this->entityID = (string) $entityID;
    }

    protected function loadEntity()
    {
        if (!isset($this->entity)) {
            $entityID = $this->entityID;
            if (empty($entityID)) {
                $entityID = $this->request->query->get('exEntityID');
                if (empty($entityID) && !empty($this->request->request->get('objectID'))) {
                    $entityID = $this->request->request->get('objectID');
                }
            }
            $entity = Express::getObjectByID($entityID);
            if (is_object($entity)) {
                $this->entity = $entity;
                $this->objectID = $this->entity->getID();
            } else {
                throw new Exception(t('Access Denied.'));
            }
        }
    }

    protected function getCustomizeResultsElement()
    {
        $provider = $this->getSearchProvider();
        $element = new CustomizeResults($provider);

        return $element;
    }

    protected function canAccess()
    {
        $this->loadEntity();
        $ep = new Permissions($this->entity);

        return $ep->canViewExpressEntries();
    }

    protected function getExpressCategory()
    {
        return new ExpressCategory($this->entity, $this->app, $this->app->make(EntityManager::class));
    }

    public function getSearchProvider()
    {
        $this->loadEntity();
        $provider = new SearchProvider($this->entity, $this->getExpressCategory(), $this->app->make('session'));

        return $provider;
    }

    public function getFieldManager()
    {
        $this->loadEntity();
        $manager = ManagerFactory::get('express');
        /*
         * @var $manager Manager
         */
        $manager->setExpressCategory($this->getExpressCategory());

        return $manager;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        $url = null;
        if (is_object($this->entity)) {
            $url = URL::to('/ccm/system/search/express/preset', $this->entity->getID(), $search->getID());
        }

        return (string) $url;
    }

    public function getCurrentSearchBaseURL()
    {
        $url = URL::to('/ccm/system/search/express/current');
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);

        return (string) $url;
    }

    public function getBasicSearchBaseURL()
    {
        $url = URL::to('/ccm/system/search/express/basic');
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);

        return (string) $url;
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/dialogs/express/advanced_search/preset/delete?presetID=' . $search->getID() . '&objectID=' . $this->objectID);
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) URL::to('/ccm/system/dialogs/express/advanced_search/preset/edit?presetID=' . $search->getID() . '&objectID=' . $this->objectID);
    }

    public function getAddFieldAction()
    {
        $action = $this->action('add_field');
        $url = LeagueUrl::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);

        return (string) $url;
    }

    public function getSubmitAction()
    {
        $action = $this->action('submit');
        $url = LeagueUrl::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);

        return (string) $url;
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository('Concrete\Core\Entity\Search\SavedExpressSearch');
        }

        return null;
    }
}
