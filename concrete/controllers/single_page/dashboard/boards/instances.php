<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;

class Instances extends DashboardSitePageController
{


    /**
     * @param $id
     * @return Board
     */
    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        $board = $r->findOneByBoardID($id);
        if ($board) {
            $checker = new Checker($board);
            if ($checker->canEditBoardSettings()) {
                return $board;
            }
        }
    }

    public function generate_instance($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('generate_instance')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$board->getSite()) {
                // This is a shared board, which means it has to specify which site its instances should be
                // created in.
                $instanceSite = null;
                if ($this->request->request->has('siteID')) {
                    $instanceSite = $this->app->make('site')->getByID($this->request->request->get('siteID'));
                }
            } else {
                $instanceSite = $board->getSite();
            }
            if (!$instanceSite) {
                $this->error->add(t('A board instance must have a site object.'));
            }

            if (!$this->error->has()) {
                $generate = new CreateBoardInstanceCommand();
                $generate->setBoardInstanceName($this->request->request->get('boardInstanceName'));
                $generate->setSite($instanceSite);
                $generate->setBoard($board);
                $this->executeCommand($generate);
                $this->flash('success', t('Board instance created.'));
                return $this->redirect('/dashboard/boards/instances/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function view($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            $instances = $this->entityManager->getRepository(Instance::class)
                ->findByBoard($board);
            $this->set('instances', $instances);
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


}
