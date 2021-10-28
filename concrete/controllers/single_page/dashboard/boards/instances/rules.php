<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards\Instances;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;

class Rules extends DashboardPageController
{

    /**
     * @param $id
     * @return Instance
     */
    protected function getInstance($id)
    {
        $r = $this->entityManager->getRepository(Instance::class);
        $instance = $r->findOneByBoardInstanceID($id);
        if ($instance) {
            $board = $instance->getBoard();
            if ($board) {
                $checker = new Checker($board);
                if ($checker->canEditBoardSettings()) {
                    return $instance;
                }
            }
        }
    }

    public function view($id = null)
    {
        $instance = $this->getInstance($id);
        if (is_object($instance)) {
            $this->set('instance', $instance);
            $this->set('rules', $instance->getRules()->toArray());
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

}
