<?php
namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\Response;
use Concrete\Core\Workflow\Request\ApproveCalendarEventRequest;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersionRepetition;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Calendar\Event\EventRepetitionService;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Calendar\Utility\Preferences;

class Edit extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/event/form';

    /**
     * @var Preferences
     */
    protected $preferences;

    /**
     * @var EventService
     */
    protected $eventService;
    /**
     * @var EventRepetitionService
     */
    protected $eventRepetitionService;
    /**
     * @var EventOccurrenceService
     */
    protected $eventOccurrenceService;

    public function __construct()
    {
        parent::__construct();
        $app = Facade::getFacadeApplication();
        $this->preferences = $app->make(Preferences::class);
        $this->eventService = $app->make(EventService::class);
        $this->eventRepetitionService = $app->make(EventRepetitionService::class);
        $this->eventOccurrenceService = $app->make(EventOccurrenceService::class);
    }

    public function add()
    {
        $calendar = Calendar::getByID($this->request->query->get('caID'));
        if (!is_object($calendar)) {
            throw new \Exception(t('Invalid calendar.'));
        }
        $this->set('calendar', $calendar);
    }

    public function edit()
    {
        if ($this->canAccess()) {
            $occurrence = $this->eventOccurrenceService->getByID($this->request->query->get('versionOccurrenceID'));
            if (!$occurrence) {
                throw new \Exception(t('Invalid occurrence.'));
            }

            $this->set('calendar', $occurrence->getEvent()->getCalendar());
            $this->set('occurrence', $occurrence);
        } else {
            die('Access Denied.');
        }
    }

    protected function canAccess()
    {
        $caID = $this->request->request->get('caID');
        if ($caID === null) {
            $caID = $this->request->query->get('caID');
        }
        $calendar = $caID ? Calendar::getByID($caID) : null;
        if (is_object($calendar)) {
            $cp = new Checker($calendar);

            return $cp->canAddCalendarEvent();
        }

        $versionOccurrenceID = $this->request->request->get('versionOccurrenceID');
        if ($versionOccurrenceID === null) {
            $versionOccurrenceID = $this->request->query->get('versionOccurrenceID');
        }
        $occurrence = $versionOccurrenceID ? $this->eventOccurrenceService->getByID($versionOccurrenceID) : null;
        if (is_object($occurrence)) {
            $calendar = $occurrence->getEvent()->getCalendar();
            if (is_object($calendar)) {
                $cp = new Checker($calendar);

                return $cp->canEditCalendarEvents();
            }
        }

        return false;
    }

    /**
     * @return ErrorList
     */
    protected function validateRequest($calendar, $repetitions)
    {
        $e = $this->app->make('error');
        if ($this->canAccess()) {
            if (!is_object($calendar)) {
                $e->add(t('Invalid calendar.'));
            }

            if (!count($repetitions)) {
                $e->add(t('You must specify a valid date for this event.'));
            }
        } else {
            $e->add(t('Access Denied.'));
        }

        return $e;
    }

    protected function addCalendarEventVersionFromRequest(
        CalendarEvent $event, $repetitions
    ) {
        $calendar = $event->getCalendar();
        $edit_type = $this->request->request->get('edit_type');
        $e = $this->validateRequest($calendar, $repetitions);
        $r = new EditResponse($e);
        if (!$e->has()) {
            $u = $this->app->make(User::class);
            $eventVersionRepetitions = array();
            if ($edit_type == 'local') {
                $event = new CalendarEvent($calendar);
            }
            $eventVersion = $this->eventService->getVersionToModify($event, $u);
            $eventVersion->setName($this->request->request->get('name'));
            $eventVersion->setDescription($this->request->request->get('description'));
            foreach($repetitions as $repetition) {
                $eventVersionRepetitions[] = new CalendarEventVersionRepetition($eventVersion, $repetition);
            }

            $permissions = new Checker($calendar);
            if ($permissions->canEditCalendarEventMoreDetailsLocation()) {
                if ($this->request->request->get('cID') !== null) {
                    $cID = intval($this->request->request->get('cID'));
                    if ($cID) {
                        $eventPage = Page::getByID($cID);
                        if (is_object($eventPage) && !$eventPage->isError()) {
                            $cp = new Checker($eventPage);
                            if ($cp->canViewPage()) {
                                $eventVersion->setRelatedPageRelationType(null);
                                $eventVersion->setPageObject($eventPage);
                            }
                        }
                    } else {
                        // Otherwise unset the page completely
                        $eventVersion->setPageID(0);
                    }
                }
            }

            if (!$eventVersion->getPageID() && $calendar->enableMoreDetails() == 'A') {
                // Associate this page with the one set at the calendar level
                $eventVersion->setPageID($calendar->getEventPageAssociatedID());
                $eventVersion->setRelatedPageRelationType('A');
            }

            $this->eventService->addEventVersion($event, $calendar, $eventVersion, $eventVersionRepetitions);

            $category = \Concrete\Core\Attribute\Key\Category::getByHandle('event');
            $sets = $category->getAttributeSets();
            foreach($sets as $set) {
                $keys = $set->getAttributeKeys();
                foreach($keys as $ak) {
                    $controller = $ak->getController();
                    $value = $controller->createAttributeValueFromRequest();
                    $eventVersion->setAttribute($ak, $value);
                }
            }

            $r->setEventVersion($eventVersion);

            // Load the local repetition if available. This is what tells us which repetition was being edited
            $localRepetition = $this->eventRepetitionService->translateFromRequest('local', $event->getCalendar(), $this->request);
            if (is_array($localRepetition) && count($localRepetition) > 0) {
                $repetition = $localRepetition[0];
            } else {
                $repetition = $repetitions[0];
            }

            $year = date('Y', strtotime($repetition->getStartDate()));
            $month = date('m', strtotime($repetition->getStartDate()));

            $this->setResponseRedirectURL($calendar, $month, $year, $r);
        }
        return $r;
    }


    protected function setResponseRedirectURL($calendar, $month, $year, EditResponse $r)
    {
        $r->setRedirectURL(
            \URL::to(
                $this->preferences->getPreferredViewPath(),
                'view',
                $calendar->getID(),
                $year,
                $month
            )
        );
    }

    public function addEvent()
    {
        if ($this->validateAction()) {
            $calendar = Calendar::getByID($this->request->request->get('caID'));
            $repetitions = $this->eventRepetitionService->translateFromRequest('event', $calendar, $this->request);
            $r = $this->addCalendarEventVersionFromRequest(new CalendarEvent($calendar), $repetitions);
            if (!$r->hasError()) {
                $version = $r->getEventVersion();
                $this->eventService->generateDefaultOccurrences($version);
                if ($this->request->request->get('publishAction') == 'approve') {
                    $u = $this->app->make(User::class);
                    $pkr = new ApproveCalendarEventRequest();
                    $pkr->setCalendarEventVersionID($r->getEventVersion()->getID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof Response) {
                        $this->flash('success', t('Event added successfully. It is published and live.'));
                    } else {
                        $this->flash(
                            'success',
                            t(
                                'Event added successfully. This event must be approved before it will be posted.'
                            )
                        );
                    }
                } else {
                    $this->flash('success', t('Event added successfully. The event is not yet published.'));
                }
            }
            $r->outputJSON();
        }
    }

    public function updateEvent()
    {
        if ($this->validateAction()) {
            $occurrence = $this->eventOccurrenceService->getByID($this->request->request->get('versionOccurrenceID'));
            if (!$occurrence) {
                throw new \Exception(t('Invalid occurrence.'));
            }
            $originalVersion = $occurrence->getVersion();
            $originalRepetitions = $originalVersion->getRepetitionEntityCollection();
            if ($this->request->request->get('edit_type') == 'local') {
                $repetitions = $this->eventRepetitionService->translateFromRequest(
                    'local',
                    $occurrence->getEvent()->getCalendar(),
                    $this->request
                );
            } else {
                $repetitions = $this->eventRepetitionService->translateFromRequest(
                    'event',
                    $occurrence->getEvent()->getCalendar(),
                    $this->request
                );
            }
            $r = $this->addCalendarEventVersionFromRequest($occurrence->getEvent(), $repetitions);
            if ($this->request->request->get('edit_type') == 'local') {
                $this->eventOccurrenceService->delete($originalVersion, $occurrence->getOccurrence());
            }
            if (!$r->hasError()) {
                if ($this->eventService->requireOccurrenceRegeneration($originalRepetitions, $repetitions)) {
                    $this->eventService->generateDefaultOccurrences($r->getEventVersion());
                }
                if ($this->request->request->get('publishAction') == 'approve') {
                    $u = $this->app->make(User::class);
                    $pkr = new ApproveCalendarEventRequest();
                    $pkr->setCalendarEventVersionID($r->getEventVersion()->getID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof Response) {
                        $this->flash('success', t('Event updated successfully. It is published and live.'));
                    } else {
                        $this->flash(
                            'success',
                            t(
                                'Event updated successfully. This event must be approved before it will be posted.'
                            )
                        );
                    }
                } else {
                    $this->flash(
                        'success',
                        t('Event updated successfully. This version of the event is not yet published.')
                    );
                }
            }
            $r->outputJSON();
        }
    }

}
