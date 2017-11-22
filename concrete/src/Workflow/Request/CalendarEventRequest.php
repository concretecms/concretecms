<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Workflow\Progress\Action\Action;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventService;

abstract class CalendarEventRequest extends Request
{
    protected $eventVersionID;

    public function setCalendarEventVersionID($eventID)
    {
        $this->eventVersionID = $eventID;
    }

    public function getCalendarEventVersionID()
    {
        return $this->eventVersionID;
    }

    public function getRequestedEventVersionObject()
    {
        return Event::getVersionByID($this->eventVersionID, EventService::EVENT_VERSION_RECENT);
    }

    public function getWorkflowRequestAdditionalActions(Progress $wp)
    {
        $buttons = array();
        $w = $wp->getWorkflowObject();

        if ($w->canApproveWorkflowProgressObject($wp)) {
            $version = $this->getRequestedEventVersionObject();
            if ($version) {

                $url = \URL::to('/ccm/calendar/dialogs/event/version/view') . '?eventVersionID=' . $version->getID();

                $button = new Action();
                $button->setWorkflowProgressActionLabel(t('View Requested Version'));
                $button->addWorkflowProgressActionButtonParameter('dialog-title', t('View Requested Version'));
                $button->addWorkflowProgressActionButtonParameter('dialog-width', '640');
                $button->addWorkflowProgressActionButtonParameter('dialog-height', '500');
                $button->setWorkflowProgressActionStyleInnerButtonLeftHTML('<i class="icon-eye-open"></i>');
                $button->setWorkflowProgressActionURL($url);
                $button->setWorkflowProgressActionStyleClass('dialog-launch');
                $buttons[] = $button;
            }
        }

        return $buttons;
    }


}
