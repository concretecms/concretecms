<?php
namespace Concrete\Controller\Element\Dashboard\Express;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Page\Page;

class Menu extends ElementController
{

    protected $currentEntity;

    public function __construct(Entity $entity)
    {
        $this->currentEntity = $entity;
    }

    public function getElement()
    {
        return 'dashboard/express/menu';
    }

    public function view()
    {
        $r = \ORM::entityManager()->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = $r->findAll(array(), array('name' => 'asc'));
        $this->set('types', $entities);
        $this->set('currentType', $this->currentEntity);
    }


}
