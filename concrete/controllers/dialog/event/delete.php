<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\DeleteCalendarEventRequest;
use Core;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Calendar\Utility\Preferences;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/delete';

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
        $this->dateFormatter = $app->make(CalendarServiceProvider::class)->getDateFormatter();
    }

    public function submit()
    {
        $event = $this->eventService->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
        $e = \Core::make('error');
        if (!$event) {
            $e->add(t('Invalid event.'));
        }
        if (!$this->canAccess()) {
            $e->add(t('Access Denied.'));
        }

        $r = new EditResponse($e);
        $year = date('Y');
        $month = date('m');
        $r->setRedirectURL(
            \URL::to(
                $this->preferences->getPreferredViewPath(),
                'view',
                $event->getCalendar()->getID(),
                $year,
                $month
            )
        );

        if (!$e->has()) {
            $u = new \User();
            $pkr = new DeleteCalendarEventRequest();
            $pkr->setCalendarEventVersionID($event->getRecentVersion()->getID());
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if ($response instanceof Response) {
                $this->flash('success', t('Event deleted successfully.'));
            } else {
                $this->flash('success', t('Event deletion pending. This request must be approved before the event is fully removed.'));
            }
        }

        $r->outputJSON();
    }


    public function view()
    {
        if ($this->canAccess()) {
            $event = $this->eventService->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
            if (!$event) {
                throw new \Exception(t('Invalid event.'));
            }
            $this->set('event', $event);
            $this->set('dateFormatter', $this->dateFormatter);
        } else {
            die('Access Denied.');
        }
    }

    protected function canAccess()
    {
        $event = $this->eventService->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
        if (is_object($event)) {
            $calendar = $event->getCalendar();
            if (is_object($calendar)) {
                $cp = new \Permissions($calendar);
                return $cp->canEditCalendarEvents();
            }
        }

        return false;
    }




}
