<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\ClearBoardDataPoolCommand;
use Concrete\Core\Board\Command\PopulateBoardDataPoolCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Pool extends DashboardSitePageController
{

    protected function getBoard($id)
    {
        $r = $this->entityManager->getRepository(Board::class);
        return $r->findOneByBoardID($id);
    }
    
    public function refresh_pool($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('refresh_pool')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            if (!$this->error->has()) {
                $clear = new ClearBoardDataPoolCommand($board);
                $populate = new PopulateBoardDataPoolCommand($board);
                $this->executeCommand($clear);
                $this->executeCommand($populate);
                $this->flash('success', t('Board data pool refreshed.'));
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
            $configuredSources = $this->entityManager->getRepository(ConfiguredDataSource::class)
                ->findByBoard($board);
            $this->set('configuredSources', $configuredSources);
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


}
