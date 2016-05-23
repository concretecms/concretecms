<?php
namespace Concrete\Controller\Element\Dashboard\Express\Entries;

use Concrete\Core\Controller\ElementController;

class Header extends ElementController
{

    protected $entity;
    protected $page;
    protected $createURL;

    /**
     * @return mixed
     */
    public function getCreateURL()
    {
        return $this->createURL;
    }

    /**
     * @param mixed $createURL
     */
    public function setCreateURL($createURL)
    {
        $this->createURL = $createURL;
    }

    public function __construct($entity, $page)
    {
        parent::__construct();
        $this->entity = $entity;
        $this->page = $page;
        $this->setCreateURL(\URL::to($page->getCollectionPath(), 'create_entry', $entity->getID()));
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }


    public function getElement()
    {
        return 'dashboard/express/entries/header';
    }

    public function view()
    {
        $this->set('entity', $this->getEntity());
        $this->set('createURL', $this->getCreateURL());
    }

}
