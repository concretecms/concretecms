<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Board\Command\DeleteBoardInstanceCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Instances extends DashboardSitePageController
{

    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        return $r->findOneByBoardID($id);
    }
    
    public function generate_instance($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('generate_instance')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $generate = new CreateBoardInstanceCommand($board);
                $this->executeCommand($generate);
                $this->flash('success', t('Board instance created.'));
                return $this->redirect('/dashboard/boards/instances/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function delete_instance($boardInstanceID = null)
    {
        $instance = $this->entityManager->find(Instance::class, $boardInstanceID);
        if ($instance) {
            $board = $instance->getBoard();
            if (!$this->token->validate('delete_instance')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->error->has()) {
                $resetCommand = new DeleteBoardInstanceCommand($instance);
                $this->executeCommand($resetCommand);

                $this->flash('success', t('Board instance removed successfully.'));
                return $this->redirect('/dashboard/boards/instances', 'view', $board->getBoardID());
            }
            $this->view($board->getId());
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
