<?
namespace Concrete\Controller\SinglePage\Dashboard\Express\Entities;

use Concrete\Controller\Element\Attribute\TypeList;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Attributes extends DashboardPageController
{

    public function view($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (is_object($entity)) {
            $attributes = $entity->getAttributes();
            $list = new TypeList();
            $list->setBaseURL('/dashboard/express/entities/attributes', 'select_type', $id);
            $this->set('list', $list);
            $this->set('attributes', $attributes);
            $this->set('pageTitle', t('Attributes'));
            $this->set('entity', $entity);
            $this->render('/dashboard/express/entities/attributes');
        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }


}
