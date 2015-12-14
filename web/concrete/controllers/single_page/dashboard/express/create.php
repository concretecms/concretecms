<?
namespace Concrete\Controller\SinglePage\Dashboard\Express;

use \Concrete\Core\Page\Controller\DashboardPageController;

class Create extends DashboardPageController
{

    public function view($id = null)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entity = $r->findOneById($id);
        if (!is_object($entity)) {
            $this->redirect('/dashboard/express');
        }
        $this->set('entity', $entity);
        $form = $entity->getForms()[0];
        $renderer = \Core::make('Concrete\Core\Express\Form\Renderer');
        $this->set('expressForm', $form);
        $this->set('renderer', $renderer);

    }



}
