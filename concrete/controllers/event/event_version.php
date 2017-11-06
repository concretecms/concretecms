<?php
namespace Concrete\Controller\Event;

use Concrete\Core\Application\Application;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\ApproveCalendarEventRequest;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\EventService;

class EventVersion extends \Concrete\Core\Controller\Controller
{

    protected $response;
    protected $eventService;

    public function __construct(Application $app)
    {
        parent::__construct();
        $service = $app->make(EventService::class);
        $e = $app->make('error');
        $version = $service->getVersionByID($this->request->request->get('eventVersionID'));
        $valid = false;
        if ($version) {
            $calendar = $version->getEvent()->getCalendar();
            if ($calendar) {
                $p = new Checker($calendar);
                if ($p->canApproveCalendarEvent()) {
                    $valid = true;
                }
            }
        }
        if (!$valid) {
            $e->add(t('Access Denied.'));
        }

        $this->response = new EditResponse($e);
        if ($version) {
            $this->response->setEventVersion($version);
        }
        $this->eventService = $service;
    }

    public function delete()
    {
        $r = $this->response;
        $e = $r->error;
        $token = $this->app->make('token');
        $versionID = 0;
        if ($r->getEventVersion()) {
            $versionID = $r->getEventVersion()->getID();
        }
        if (!$token->validate('calendar/event/version/delete/' . $versionID)) {
            $e->add($token->getErrorMessage());
        }
        if (!$e->has()) {
            $this->eventService->deleteVersion($r->getEventVersion());
            $r->setMessage(t('Version deleted successfully.'));
        }

        $r->outputJSON();
    }

    public function approve()
    {
        $r = $this->response;
        $e = $r->error;
        $token = $this->app->make('token');
        $versionID = 0;
        if ($r->getEventVersion()) {
            $versionID = $r->getEventVersion()->getID();
        }
        if (!$token->validate('calendar/event/version/approve/' . $versionID)) {
            $e->add($token->getErrorMessage());
        }
        if (!$e->has()) {
            $u = new \User();
            $pkr = new ApproveCalendarEventRequest();
            $pkr->setCalendarEventVersionID($r->getEventVersion()->getID());
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if ($response instanceof Response) {
                $r->setMessage(t('Event version updated successfully. It is published and live.'));
            } else {
                $r->setMessage(t('Event approval updated requested. This event must be approved before it will be posted.'));
            }
        }

        $r->outputJSON();
    }


}
