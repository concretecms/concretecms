<?php
namespace Concrete\Controller\Dialog\Express;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\Express\Search\Field\Manager;
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
        $entity = \Express::getObjectByID($this->request->query->get('exEntityID'));
        $this->entity = $entity;
    }


    protected function canAccess()
    {
        $this->loadEntity();
        $ep = new \Permissions($this->entity);
        return $ep->canViewExpressEntries();
    }

    public function getSearchProvider()
    {
        return $this->app->make('Concrete\Core\User\Search\SearchProvider');
    }

    public function getFieldManager()
    {
        $manager = ManagerFactory::get('express');
        /**
         * @var $manager Manager
         */
        $manager->setExpressCategory(new ExpressCategory($this->entity, $this->app, $this->app->make(EntityManager::class)));
        return $manager;
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return false;
    }

    public function getCurrentSearchBaseURL()
    {
        return \URL::to('/ccm/system/search/express/current', $this->entity->getID());
    }

    public function getBasicSearchBaseURL()
    {
        return \URL::to('/ccm/system/search/users/basic', $this->entity->getID());
    }

    public function getAddFieldBaseURL()
    {
        $action = $this->action('add_field');
        $url = Url::createFromUrl($action);
        $url->getQuery()->set(['exEntityID' => $this->entity->getID()]);
        return (string) $url;
    }
}
