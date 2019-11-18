<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class DataSources extends DashboardSitePageController
{
    public function view($id = null)
    {
        $r = $this->entityManager->getRepository(Board::class);
        $board = $r->findOneByBoardID($id);
        if (is_object($board)) {
            $sources = $this->entityManager->getRepository(DataSource::class)->findAll();
            $this->set('sources', $sources);
            $this->set('board', $board);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

}
