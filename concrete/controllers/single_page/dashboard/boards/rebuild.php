<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Populator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Rebuild extends DashboardSitePageController
{

    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        return $r->findOneByBoardID($id);
    }
    
    public function rebuild_board($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('rebuild_board')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $populator = $this->app->make(Populator::class);
                $populator->rebuild($board);
                $this->flash('success', t('Board rebuilt.'));
                return $this->redirect('/dashboard/boards/details/', 'view', $board->getBoardID());
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
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


}
