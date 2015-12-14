<?
namespace Concrete\Controller\SinglePage\Dashboard;

use \Concrete\Core\Page\Controller\DashboardPageController;

class Express extends DashboardPageController
{

    public function view()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entity');
        $entities = $r->findAll(array(), array('name' => 'asc'));
        if (count($entities)) {
            $this->redirect('/dashboard/express/entries', $entities[0]->getID());
        } else {
            $this->redirect('/dashboard/express/entities');
        }
    }


}
