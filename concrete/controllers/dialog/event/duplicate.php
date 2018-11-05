<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Calendar\Calendar\CalendarService;
use Concrete\Core\Calendar\Utility\Preferences;

class Duplicate extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/duplicate';

    /**
     * @var EventService
     */
    protected $eventService;


    public function submit()
    {
        if ($this->canAccess()) {

            $e = $this->app->make('error');
            $calendar = $this->app->make(CalendarService::class)->getByID($_REQUEST['caID']);
            if ($calendar) {
                $cp = new \Permissions($calendar);
                if (!$cp->canAddCalendarEvent()) {
                    $e->add(t('You do not have access to add an event to this calendar.'));
                }
            } else {
                $e->add(t('Invalid calendar.'));
            }

            $r = new EditResponse($e);
            if (!$e->has()) {

                $service = $this->app->make(EventService::class);
                $event = $service->getByID($_REQUEST['eventID']);
                $u = new \User();

                $event = $service->duplicate($event, $u, $calendar);

                $datetime = new \DateTime('now', new \DateTimeZone($calendar->getTimezone()));
                $year = $this->request->request->has('year') ?
                    intval($this->request->request->get('year')) :
                    $datetime->format('Y');
                $month = $this->request->request->has('month') ?
                    intval($this->request->request->get('month')) :
                    $datetime->format('m');

                $r->setRedirectURL(
                    \URL::to(
                        $this->app->make(Preferences::class)->getPreferredViewPath(),
                        'view',
                        intval($_REQUEST['caID']),
                        $year,
                        $month
                    )
                );
                $this->flash('success', t('Event duplicated. The new event has been saved but has not yet been approved.'));
            }
            $r->outputJSON();

        } else {
            throw new \Exception(t('Access Denied.'));
        }
    }

    public function view()
    {
        if ($this->canAccess()) {
            $event = $this->app->make(EventService::class)->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
            $calendars = [];
            /**
             * @var $service CalendarService
             */
            $service = $this->app->make(CalendarService::class);
            foreach($service->getList() as $calendar) {
                $cp = new \Permissions($calendar);
                if ($cp->canAddCalendarEvent()) {
                    $calendars[$calendar->getID()] = $calendar->getName();
                }
            }
            $this->set('event', $event);
            $this->set('form', $this->app->make(Form::class));
            $this->set('caID', $event->getCalendar()->getID());
            $this->set('calendars', $calendars);
            $year = false;
            if ($this->request->query->has('year')) {
                $year = $this->request->query->get('year');
            }
            if ($this->request->query->has('month')) {
                $month = $this->request->query->get('month');
            }
            $this->set('year', $year);
            $this->set('month', $month);
        } else {
            throw new \Exception('Access Denied.');
        }
    }

    protected function canAccess()
    {
        $event = $this->app->make(EventService::class)->getByID($_REQUEST['eventID'], EventService::EVENT_VERSION_RECENT);
        if ($event) {
            $calendar = $event->getCalendar();
            if (is_object($calendar)) {
                $p = new \Permissions($calendar);
                if ($p->canCopyCalendarEvents()) {
                    return true;
                }
            }
        }
        return false;
    }




}
