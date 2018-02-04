<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Support\Facade\Facade;
use Core;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\CalendarServiceProvider;
use Concrete\Core\Calendar\Event\EventService;

class Versions extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/versions';

    /**
     * @var EventService
     */
    protected $eventService;

    public function __construct()
    {
        parent::__construct();
        $app = Facade::getFacadeApplication();
        $this->eventService = $app->make(EventService::class);
        $this->dateFormatter = $app->make(CalendarServiceProvider::class)->getDateFormatter();
    }

    public function view()
    {
        if ($this->canAccess()) {
            $event = $this->eventService->getByID($_REQUEST['eventID']);
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
        $event = $this->eventService->getByID($_REQUEST['eventID']);
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
