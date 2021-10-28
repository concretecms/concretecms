<?php
namespace Concrete\Controller\Dialog\Board;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\View\DialogView;
use Doctrine\ORM\EntityManagerInterface;

class Permissions extends \Concrete\Core\Controller\Controller
{
    public function view($pkCategoryHandle)
    {
        if ($this->request->query->has('boardID')) {
            $board = $this->app->make(EntityManagerInterface::class)->find(Board::class, $this->request->query->get('boardID'));
            if ($board === null) {
                throw new UserMessageException(t('Failed to find the board requested.'));
            }
            $this->set('board', $board);
        }
        $v = new DialogView('/dialogs/permissions/' . $pkCategoryHandle);
        $v->setController($this);
        return $v;
    }
}
