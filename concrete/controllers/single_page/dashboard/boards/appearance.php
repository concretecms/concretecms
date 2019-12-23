<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Board\Command\DisableCustomSlotTemplatesCommand;
use Concrete\Core\Board\Command\EnableCustomSlotTemplatesCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;

class Appearance extends DashboardSitePageController
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
            $templates = $this->entityManager->getRepository(SlotTemplate::class)
                ->findAll();
            $selectedTemplateIDs = [];
            foreach($board->getCustomSlotTemplates() as $template) {
                $selectedTemplateIDs[] = $template->getId();
            }
            $this->set('selectedTemplateIDs', $selectedTemplateIDs);
            $this->set('templates', $templates);
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function update_appearance($id = null)
    {
        $board = $this->getBoard($id);
        if (is_object($board)) {
            if (!$this->token->validate('update_appearance')) {
                $this->error->add(t($this->token->getErrorMessage()));
            }

            if (!$this->error->has()) {

                if ($this->request->request->get('hasCustomSlotTemplates')) {
                    $command = new EnableCustomSlotTemplatesCommand();
                    $command->setBoard($board);
                    $templateIDs = $this->request->request->get('templateIDs');
                    if ($templateIDs) {
                        $command->setTemplateIDs($templateIDs);
                    }
                } else {
                    $command = new DisableCustomSlotTemplatesCommand();
                    $command->setBoard($board);
                }
                $this->executeCommand($command);
                $this->flash('success', t('Board appearance saved.'));
                return $this->redirect('/dashboard/boards/details/', 'view', $board->getBoardID());
            }
            $this->view($id);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }


    }


}
