<?php
namespace Concrete\Controller\Dialog\Frontend;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Block\Block;
use Concrete\Core\Calendar\Event\EventOccurrence;

class Event extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/frontend/view';

    public function view($bID, $occurrence_id)
    {
        $b = Block::getByID($bID);
        $b->setBlockActionCollectionID($b->getBlockCollectionID());

        if (is_object($b) && $b->getBlockTypeHandle() == 'calendar') {
            $controller = $b->getController();
            $occurrence = EventOccurrence::getByID($occurrence_id);
            $this->set('occurrence', $occurrence);
            $this->set('blockController', $controller);
        }
    }

    protected function canAccess()
    {
        return true;
    }
}
