<?
namespace Concrete\Controller\SinglePage\Dashboard\Express\Entities;

use \Concrete\Core\Page\Controller\DashboardPageController;

class Associations extends DashboardPageController
{

    public function view($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (is_object($entity)) {
            $this->set('entity', $entity);
            $this->set('pageTitle', t('Associations'));
            $this->render('/dashboard/express/entities/associations');
        } else {
            $this->redirect('/dashboard/system/express');
        }
    }



}
