<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\ApproveCalendarEventRequest;
use Core;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Calendar\Utility\Preferences;

class DeleteOccurrence extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/delete_occurrence';

    /**
     * @var Preferences
     */
    protected $preferences;

    /**
     * @var EventService
     */
    protected $eventService;

    public function __construct()
    {
        parent::__construct();
        $app = Facade::getFacadeApplication();
        $this->preferences = $app->make(Preferences::class);
        $this->eventService = $app->make(EventService::class);
        $this->eventOccurrenceService = $app->make(EventOccurrenceService::class);
        $this->dateFormatter = $app->make(CalendarServiceProvider::class)->getDateFormatter();
    }

    public function submit()
    {
        $occurrence = $this->eventOccurrenceService->getByID($this->request->request->get('versionOccurrenceID'));
        $e = \Core::make('error');
        if (!$occurrence) {
            $e->add(t('Invalid occurrence.'));
        }
        if (!$this->canAccess()) {
            $e->add(t('Access Denied.'));
        }

        $r = new EditResponse($e);
        $year = date('Y', $occurrence->getStart());
        $month = date('m', $occurrence->getStart());
        $r->setRedirectURL(
            \URL::to(
                $this->preferences->getPreferredViewPath(),
                'view',
                $occurrence->getEvent()->getCalendar()->getID(),
                $year,
                $month
            )
        );

        if (!$e->has()) {
            $u = new \User();
            $eventVersion = $this->eventService->getVersionToModify($occurrence->getEvent(), $u);
            $this->eventService->addEventVersion($eventVersion->getEvent(), $eventVersion->getEvent()->getCalendar(), $eventVersion);
            $this->eventOccurrenceService->delete($eventVersion, $occurrence->getOccurrence());

            $pkr = new ApproveCalendarEventRequest();
            $pkr->setCalendarEventVersionID($eventVersion->getID());
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if ($response instanceof Response) {
                $this->flash('success', t('Event occurrence removed.'));
            } else {
                $this->flash('success', t('Event occurrence cancellation requested. This must be approved before it is fully removed.'));
            }
        }

        $r->outputJSON();
    }


    public function view()
    {
        if ($this->canAccess()) {
            $occurrence = $this->eventOccurrenceService->getByID($this->request->query->get('versionOccurrenceID'));
            if (!$occurrence) {
                throw new \Exception(t('Invalid occurrence.'));
            }
            $this->set('occurrence', $occurrence);
            $this->set('dateFormatter', $this->dateFormatter);
        } else {
            die('Access Denied.');
        }
    }

    protected function canAccess()
    {
        $occurrence = $this->eventOccurrenceService->getByID($_REQUEST['versionOccurrenceID']);
        if (is_object($occurrence)) {
            $calendar = $occurrence->getEvent()->getCalendar();
            if (is_object($calendar)) {
                $cp = new \Permissions($calendar);
                return $cp->canEditCalendarEvents();
            }
        }

        return false;
    }




}
