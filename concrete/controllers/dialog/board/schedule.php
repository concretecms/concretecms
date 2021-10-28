<?php
namespace Concrete\Controller\Dialog\Board;

use Concrete\Controller\Backend\UserInterface  as Controller;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;

class Schedule extends Controller
{

    protected $viewPath = '/dialogs/boards/schedule';

    /**
     * @var Instance|null
     */
    protected $boardInstance;

    protected function canAccess()
    {
        $boardInstanceID = $this->request->attributes->get('boardInstanceID');
        $em = $this->app->make(EntityManager::class);
        if ($boardInstanceID) {
            $boardInstance = $em->find(Instance::class, $boardInstanceID);
            if ($boardInstance) {
                $board = $boardInstance->getBoard();
                $permissions = new Checker($board);
                if ($permissions->canEditBoardContents()) {
                    $this->boardInstance = $boardInstance;
                    return true;
                }
            }
        }
        return false;
    }

    public function view($boardInstanceID)
    {
        $this->set('boardInstance', $this->boardInstance);
    }
}
