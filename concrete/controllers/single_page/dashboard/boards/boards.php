<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Page\Controller\DashboardPageController;

class Boards extends DashboardPageController
{
    public function view()
    {
        $r = $this->entityManager->getRepository(Board::class);
        $boards = $r->findBy([], ['boardName' => 'asc']);
        $this->set('boards', $boards);
    }
}
