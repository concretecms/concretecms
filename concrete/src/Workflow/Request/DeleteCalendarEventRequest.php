<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Workflow\Description;
use Concrete\Core\Workflow\Progress\Progress;
use Concrete\Core\Workflow\Progress\Response;
use HtmlObject\Element;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventService;

class DeleteCalendarEventRequest extends ApproveCalendarEventRequest
{

    protected function isNewEventRequest()
    {
        $version = $this->getRequestedEventVersionObject();
        $event = $version->getEvent();
        $active = Event::getByID($event->getID());
        if ($active) {
            if ($active->getSelectedVersion()) {
                return false;
            }
        }
        return true;
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new Description();
        $v = $this->getRequestedEventVersionObject();
        if (is_object($v)) {
            $d->setEmailDescription(t("\"%s\" has been marked for deletion.", $v->getName()));
            $d->setDescription(t("%s submitted for Deletion.", $v->getName()));
            $d->setInContextDescription(t("Event %s Submitted for Deletion.", $v->getName()));
            $d->setShortStatus(t("Pending Delete"));
        }

        return $d;
    }

    public function getWorkflowRequestStyleClass()
    {
        return 'danger';
    }

    public function getWorkflowRequestApproveButtonClass()
    {
        return '';
    }

    public function getWorkflowRequestApproveButtonInnerButtonRightHTML()
    {
        return '<i class="fa fa-trash-o"></i>';
    }

    public function getWorkflowRequestApproveButtonText()
    {
        return t('Delete');
    }

    public function getRequestIconElement()
    {
        $span = new Element('i');
        $span->addClass('fa fa-calendar-o');

        return $span;
    }

    public function approve(Progress $wp)
    {
        $version = $this->getRequestedEventVersionObject();
        if ($version) {
            $event = $version->getEvent();
            $service = \Core::make(EventService::class);
            $service->delete($event);
        }
        $wpr = new Response();

        return $wpr;
    }

}
