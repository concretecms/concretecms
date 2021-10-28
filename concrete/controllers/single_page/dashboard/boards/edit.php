<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\DeleteBoardCommand;
use Concrete\Core\Board\Command\ResetBoardCustomWeightingCommand;
use Concrete\Core\Board\Command\UpdateBoardCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Edit extends Add
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
    
    public function view($id = null)
    {
        parent::view();
        $board = $this->getBoard($id);
        if (is_object($board)) {
            $this->set('board', $board);
            $this->set('sortBy', $board->getSortBy());
            $this->set('boardName', $board->getBoardName());
            $this->set('templateID', $board->getTemplate()->getID());
            $this->set('isSharedBoard', !$board->getSite());
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function delete_board($id = null)
    {
        $board = $this->getBoard($id);
        if ($board) {
            if (!$this->token->validate('delete_board')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->error->has()) {
                $command = new DeleteBoardCommand();
                $command->setBoard($board);

                $this->executeCommand($command);

                $this->flash('success', t('Board removed successfully.'));
                return $this->redirect('/dashboard/boards/boards', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


    public function submit($id = null)
    {
        list($name, $template) = $this->validateBoardRequest();
        $this->view($id);
        if (!$this->error->has()) {
            $command = new UpdateBoardCommand();
            $command->setBoard($this->get('board'));
            if (empty($this->request->request->get('sharedBoard'))) {
                $command->setSite($this->getSite());
            }
            $sortBy = 'relevant_date_asc';
            if (in_array($this->request->request->get('sortBy'), ['relevant_date_desc'])) {
                $sortBy = $this->request->request->get('sortBy');
            }
            $command->setSortBy($sortBy);
            $command->setName($name);
            $command->setTemplate($template);
            $board = $this->executeCommand($command);

            $this->flash('success', t('Board updated successfully.'));
            $this->redirect('/dashboard/boards/details', 'view', $board->getBoardID());
        }
        $this->view();
    }
}
