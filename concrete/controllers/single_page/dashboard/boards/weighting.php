<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\ResetBoardCustomWeightingCommand;
use Concrete\Core\Board\Command\SetBoardCustomWeightingCommand;
use Concrete\Core\Board\Command\SetBoardCustomWeightingCommandValidator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;

class Weighting extends DashboardSitePageController
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

    public function update_weighting($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('update_weighting')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            $command = new SetBoardCustomWeightingCommand();
            $command->setBoard($board);
            $configuredSources = $this->entityManager->getRepository(ConfiguredDataSource::class)
                ->findByBoard($board);
            $weighting = $this->request->request->get('weighting');
            foreach($configuredSources as $configuredSource) {
                $weight = 0;
                if (is_array($weighting) && isset($weighting[$configuredSource->getConfiguredDataSourceID()])) {
                    $weight = (int) $weighting[$configuredSource->getConfiguredDataSourceID()];
                }
                $command->addWeighting($configuredSource, $weight);
            }
            $validator = $this->app->make(SetBoardCustomWeightingCommandValidator::class);
            $errorList = $validator->validate($command);
            
            if ($errorList->has()) {
                $this->error->add($errorList);
            }
            
            if (!$this->error->has()) {
                $this->executeCommand($command);
                $this->flash('success', t('Board weighting saved.'));
                return $this->redirect('/dashboard/boards/details/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }


    }

    public function reset_weighting($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('reset_weighting')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }
            $command = new ResetBoardCustomWeightingCommand();
            $command->setBoard($board);
            if (!$this->error->has()) {
                $this->executeCommand($command);
                $this->flash('success', t('Board weighting reset.'));
                return $this->redirect('/dashboard/boards/details/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }


    }


}
