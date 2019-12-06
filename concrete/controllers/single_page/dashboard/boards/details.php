<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Details extends DashboardSitePageController
{
    public function view($id = null)
    {
        $r = $this->entityManager->getRepository(Board::class);
        $board = $r->findOneByBoardID($id);
        if (is_object($board)) {
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
