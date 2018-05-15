<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Workflow\Description;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Workflow;
use Concrete\Core\Workflow\Progress\CalendarEventProgress;
use HtmlObject\Element;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventService;

class ApproveCalendarEventRequest extends CalendarEventRequest
{

    public function __construct()
    {
        $pk = Key::getByHandle('approve_calendar_event');
        parent::__construct($pk);
    }

    public function trigger()
    {
        $version = $this->getRequestedEventVersionObject();
        if ($version) {
            $event = $version->getEvent();
            if ($event) {
                $calendar = $event->getCalendar();
                $pk = $this->getWorkflowRequestPermissionKeyObject();
                $pk->setPermissionObject($calendar);

                return parent::triggerRequest($pk);
            }
        }
    }

    protected function isNewEventRequest()
    {
        $version = $this->getRequestedEventVersionObject();
        if ($version) {
            $event = $version->getEvent();
            if ($event) {
                $active = Event::getByID($event->getID());
                if ($active) {
                    if ($active->getSelectedVersion()) {
                        return false;
                    }
                }
                return true;
            }
        }
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new Description();
        $v = $this->getRequestedEventVersionObject();
        if (is_object($v)) {
            $url = \URL::to('/ccm/calendar/dialogs/event/version/view') . '?eventVersionID=' . $v->getID();

            if (!$this->isNewEventRequest()) {
                // new version of existing page
                $d->setEmailDescription(t("\"%s\" has pending changes and needs to be approved.", $v->getName()));
                $d->setDescription(t(
                    'Version %1$s of Event %2$s submitted for Approval.',
                    '<a href="' . $url . '" dialog-title="' . t('View Requested Version') . '" dialog-width="640" dialog-height="500" class="dialog-launch">' . $this->eventVersionID . '</a>',
                    '<strong>' . $v->getName() . '</strong>'
                ));
                $d->setInContextDescription(t("Event Version %s Submitted for Approval.", $v->getName()));
                $d->setShortStatus(t("Pending Approval"));
            } else {
                // Completely new page.
                $d->setEmailDescription(t("New event created: \"%s\". This event requires approval.", $v->getName()));
                $d->setDescription(t(
                    'New Event: %s',
                    '<a href="' . $url . '" dialog-title="' . t('View Requested Version') . '" dialog-width="640" dialog-height="500" class="dialog-launch">' . $v->getName() . '</a>'
                ));
                $d->setInContextDescription(t("New Event %s submitted for approval.", $v->getName()));
                $d->setShortStatus(t("New Event"));
            }
        }

        return $d;
    }

    public function getWorkflowRequestStyleClass()
    {
        return 'info';
    }

    public function getWorkflowRequestApproveButtonClass()
    {
        return '';
    }

    public function getWorkflowRequestApproveButtonInnerButtonRightHTML()
    {
        return '<i class="fa fa-thumbs-o-up"></i>';
    }

    public function getWorkflowRequestApproveButtonText()
    {
        return t('Approve');
    }

    public function getRequestIconElement()
    {
        $span = new Element('i');
        $span->addClass('fa fa-calendar-o');

        return $span;
    }

    public function addWorkflowProgress(Workflow $wf)
    {
        $pwp = CalendarEventProgress::add($wf, $this);
        $r = $pwp->start();
        $pwp->setWorkflowProgressResponseObject($r);

        return $pwp;
    }

    public function approve(Progress $wp)
    {
        $version = $this->getRequestedEventVersionObject();
        if ($version) {
            $service = \Core::make(EventService::class);
            $service->approve($version);
        }
        $wpr = new Response();

        return $wpr;
    }
}
