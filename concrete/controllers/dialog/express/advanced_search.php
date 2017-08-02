<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Controller\Element\Search\Express\CustomizeResults;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Express\Search\Field\Manager;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;
use Doctrine\ORM\EntityManager;
use League\Url\Url;

class AdvancedSearch extends AdvancedSearchController
{

    protected $entity;

    protected $supportsSavedSearch = false;

    public function view()
    {
        $this->loadEntity();
        parent::view();
    }

    protected function loadEntity()
    {
        if (!isset($this->entity)) {
            $entity = \Express::getObjectByID($this->request->query->get('exEntityID'));
            $this->entity = $entity;
            if (!$this->entity) {
                throw new \Exception(t('Access Denied.'));
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
        $ep = new \Permissions($this->entity);
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
        /**
         * @var $manager Manager
         */
        $manager->setExpressCategory($this->getExpressCategory());
        return $manager;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return false;
    }

    public function getCurrentSearchBaseURL()
    {
        $url = \URL::to('/ccm/system/search/express/current');
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }

    public function getBasicSearchBaseURL()
    {
        $url = \URL::to('/ccm/system/search/express/basic');
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }

    public function getAddFieldAction()
    {
        $action = $this->action('add_field');
        $url = Url::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }

    public function getSubmitAction()
    {
        $action = $this->action('submit');
        $url = Url::createFromUrl($action);
        $url->getQuery()->modify(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }

}
