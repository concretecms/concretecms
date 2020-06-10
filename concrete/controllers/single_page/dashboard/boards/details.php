<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Details extends DashboardSitePageController
{
    
    public function view($id = null)
    {
        $r = $this->entityManager->getRepository(Board::class);
        $board = $r->findOneByBoardID($id);
        if ($board) {
            $permissions = new Checker($board);
            if (!$permissions->canViewBoard()) {
                unset($board);
            }
        }
        if ($board) {
            $template = $board->getTemplate();
            $templateDriver = $template->getDriver();
            $this->set('template', $template);
            $this->set('templateDriver', $templateDriver);
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

}
