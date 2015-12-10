<?
namespace Concrete\Controller\SinglePage\Dashboard\Express\Entities;

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardAttributesPageController;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Attributes extends DashboardAttributesPageController
{

    protected function getEntity($id)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        return $r->findOneById($id);
    }

    public function view($id = null)
    {
        $entity = $this->getEntity($id);
        $this->set('entity', $entity);
        $this->renderList($entity->getAttributes(), Type::getAttributeTypeList());
    }

    public function edit($id = null, $akID = null)
    {
        $this->set('entity', $this->getEntity($id));
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\AttributeKey\AttributeKey');
        $key = $r->findOneBy(array('akID' => $akID));
        $this->renderEdit($key,
            \URL::to('/dashboard/express/entities/attributes', 'view', $id)
        );
    }

    public function select_type($id = null, $type = null)
    {
        $this->set('entity', $this->getEntity($id));
        $type = Type::getByID($type);
        $this->renderAdd($type,
            \URL::to('/dashboard/express/entities/attributes', 'view', $id)
        );
    }

    public function add($id = null, $type = null)
    {
        $this->select_type($id, $type);
        $type = Type::getByID($type);
        $entity = $this->getEntity($id);
        $this->set('entity', $entity);
        $this->executeAdd($entity, $type, \URL::to('/dashboard/express/entities/attributes', 'view', $id));
    }

    public function delete($id = null, $akID = null)
    {
        $entity = $this->getEntity($id);
        $this->set('entity', $entity);
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\AttributeKey\AttributeKey');
        $key = $r->findOneBy(array('akID' => $akID));
        $this->executeDelete($entity, $key,
            \URL::to('/dashboard/express/entities/attributes', 'view', $id)
        );
    }



}
