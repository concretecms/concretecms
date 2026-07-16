<?php
namespace Concrete\Controller\Dialog\Frontend;

use Concrete\Block\Calendar\Controller;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Block\Block;
use Concrete\Core\Calendar\Event\EventOccurrence;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;

class Event extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/frontend/view';

    public function view($bID, $occurrence_id)
    {
        $b = Block::getByID($bID);
        $b->setBlockActionCollectionID($b->getBlockCollectionID());

        if (is_object($b) && $b->getBlockTypeHandle() == 'calendar') {
            $controller = $b->getController();
            /**
             * @var $controller Controller
             */
            $calendar = $controller->getCalendar();
            if (!$calendar) {
                throw new UserMessageException(t('Invalid calendar.'));
            }
            $checker = new Checker($calendar);
            if (!$checker->canViewCalendar()) {
                throw new UserMessageException(t('Access Denied.'));
            }
            $occurrence = EventOccurrence::getByID($occurrence_id);
            if (!$occurrence || $occurrence->getEvent()->getCalendar()->getID() !== $calendar->getID()) {
                throw new UserMessageException(t('Invalid occurrence.'));
            }
            $this->set('occurrence', $occurrence);
            $this->set('blockController', $controller);
        }
    }

    protected function canAccess()
    {
        return true;
    }
}
