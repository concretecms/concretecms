<?php
namespace Concrete\Controller\Event;

use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\UnapproveCalendarEventRequest;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\EventService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Calendar\Event\Event as CalendarEvent;

class Event extends \Concrete\Core\Controller\Controller
{
    public function getJSON()
    {
        $event = CalendarEvent::getByID($this->request->request->get('eventID'));
        if (is_object($event)) {
            $calendar = $event->getCalendar();
            if (is_object($calendar)) {
                $p = new \Permissions($calendar);
                if ($p->canViewCalendarInEditInterface()) {
                    $obj = new \stdClass();
                    $obj->id = $event->getID();
                    $obj->title = $event->getName();

                    return new JsonResponse($obj);
                }
            }
        }
        throw new \Exception(t('Access Denied'));
    }

    public function unapprove()
    {
        $event = CalendarEvent::getByID($this->request->request->get('eventID'));
        if ($event) {
            $calendar = $event->getCalendar();
            if (is_object($calendar)) {
                $p = new \Permissions($calendar);
                if ($p->canApproveCalendarEvent() && $this->app->make('token')->validate("unapprove_event")) {

                    $r = new EditResponse();
                    $r->setEventVersion($event->getRecentVersion());
                    $u = new \User();
                    $pkr = new UnapproveCalendarEventRequest();
                    $version = $event->getApprovedVersion();
                    if (!$version) {
                        $version = $event->getRecentVersion();
                    }
                    $pkr->setCalendarEventVersionID($version->getID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof Response) {
                        $r->setMessage(t('All versions of the event have been unapproved.'));
                    } else {
                        $r->setMessage(t('Event unapproval requested. This must be approved before the event is unapproved.'));
                    }

                    $r->outputJSON();
                }
            }
        }
        throw new \Exception(t('Access Denied'));
    }


}
