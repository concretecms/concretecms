<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class Add extends DashboardSitePageController
{
    public function view()
    {
    }

    public function submit()
    {
        $security = new SanitizeService();
        $strings = $this->app->make(Strings::class);
        $name = $security->sanitizeString($this->post('boardName'));
        if (!$this->token->validate('submit')) {
            $this->error->add(t($this->token->getErrorMessage()));
        }

        if (!$strings->notempty($name)) {
            $this->error->add(t('You must specify a valid name for your board.'));
        }
        
        if (!$this->error->has()) {
            $board = new Board();
            $board->setSite($this->getSite());
            $board->setBoardName($name);
            $this->entityManager->persist($board);
            $this->entityManager->flush();
            $this->redirect('/dashboard/boards/details', 'view', $board->getBoardID());
        }
        $this->view();
    }
}
