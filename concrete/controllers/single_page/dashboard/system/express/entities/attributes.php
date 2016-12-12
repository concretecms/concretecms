<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express\Entities;

use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;

class Attributes extends DashboardAttributesPageController
{
    protected $category;

    protected function getEntity($id)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $this->category = $r->findOneById($id);
        return $this->category;
    }

    protected function getCategoryObject()
    {
        return $this->category;
    }

    public function view($id = null)
    {
        $entity = $this->getEntity($id);
        $this->set('entity', $entity);
        $this->renderList($entity->getAttributes(), Type::getAttributeTypeList());
    }

    protected function getHeaderMenu(CategoryObjectInterface $category)
    {
        return false;
    }

    public function edit($id = null, $akID = null)
    {
        $this->set('entity', $this->getEntity($id));
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\Key');
        $key = $r->findOneBy(array('akID' => $akID));
        $this->renderEdit($key,
            \URL::to('/dashboard/system/express/entities/attributes', 'view', $id)
        );
    }

    public function update($id = null, $akID = null)
    {
        $this->edit($id, $akID);
        $entity = $this->getEntity($id);
        $this->set('entity', $entity);
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\Key');
        $key = $r->findOneBy(array('akID' => $akID));
        $this->executeUpdate($key,
            \URL::to('/dashboard/system/express/entities/attributes', 'view', $id)
        );
    }

    public function select_type($id = null, $type = null)
    {
        $this->set('entity', $this->getEntity($id));
        $type = Type::getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/system/express/entities/attributes', 'view', $id)
        );
    }

    public function add($id = null, $type = null)
    {
        $this->select_type($id, $type);
        $type = Type::getByID($type);
        $entity = $this->getEntity($id);
        $this->set('entity', $entity);
        $this->executeAdd($type, \URL::to('/dashboard/system/express/entities/attributes', 'view', $id));
    }

    public function delete($id = null, $akID = null)
    {
        $entity = $this->getEntity($id);
        $factory = $this->app->make('Concrete\Core\Attribute\Category\ExpressCategory');
        $key = $factory->getAttributeKeyByID($akID);
        $this->set('entity', $entity);
        $this->executeDelete($key,
            \URL::to('/dashboard/system/express/entities/attributes', 'view', $id)
        );
    }
}
