<?
namespace Concrete\Controller\Dialog\Calendar;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Event\Event as CalendarEvent;
use Concrete\Core\Calendar\Event\EventOccurrence;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Form\Service\Widget\DateTime;
use RedirectResponse;

class Event extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/calendar/event/form';

    public function add($caID)
    {
        $calendar = Calendar::getByID($caID);
        if (!is_object($calendar)) {
            throw new \Exception(t('Invalid calendar.'));
        }
    }

    public function edit($occurrence_id = 0)
    {
        if ($this->canAccess(false)) {

            $occurrence = EventOccurrence::getByID($occurrence_id);
            if (!$occurrence) {
                throw new \Exception(t('Invalid occurrence.'));
            }

            $this->set('occurrence', $occurrence);
        } else {
            die('Access Denied.');
        }
    }

    protected function canAccess()
    {
        $c = \Page::getByPath('/dashboard/calendar/events');
        $cp = new \Permissions($c);
        return $cp->canViewPage();
    }

    public function cancel($occurrence_id)
    {
        if ($this->canAccess()) {

            $occurrence = EventOccurrence::getByID($occurrence_id);
            if (!$occurrence) {
                throw new \Exception(t('Invalid occurrence.'));
            }

            $occurrence->cancel();
            $occurrence->save();

            $year = date('Y', $occurrence->getStart());
            $month = date('m', $occurrence->getStart());
            $response = new \RedirectResponse(
                \URL::to(
                    '/dashboard/calendar/events/',
                    'view',
                    $occurrence->getEvent()->getCalendar()->getID(),
                    $year,
                    $month,
                    'occurrence_cancelled'
                ));
            $response->send();
        } else {
            die('Access denied');
        }
    }

    public function save($occurrence_id)
    {
        $e = \Core::make('error');

        if (!$this->canAccess()) {
            $e->add('Access denied.');
            $r = new EditResponse($e);
            $r->outputJSON();
            exit;
        }

        $repetition = null;
        $edit_type = $this->request->request->get('edit_type');
        if ($edit_type != 'local') {
            $repetition = EventRepetition::translateFromRequest($this->request, $edit_type == 'forward');
            if (!is_object($repetition)) {
                $e->add(t('You must specify a valid date for this event.'));
            }
        }

        $occurrence = EventOccurrence::getByID($occurrence_id);
        if (!$occurrence) {
            throw new \Exception(t('Invalid occurrence.'));
        }

        $calendar = $occurrence->getEvent()->getCalendar();
        $r = new EditResponse($e);

        if (!$e->has()) {
            if (!$occurrence->getEvent()->getRepetition()->repeats()) {
                $repetition = EventRepetition::translateFromRequest($this->request);
                $repetition->save();

                /** @var \Concrete\Core\Calendar\Event\Event $ev */
                $ev = $occurrence->getEvent();
                $ev->setName($this->request->request->get('name'));
                $ev->setDescription($this->request->request->get('description'));
                $rep = $ev->getRepetition();
                $ev->setRepetition($repetition);
                $ev->save();

                $now = $occurrence->getStart();
                $db = \Database::connection();
                $db->query(
                    'DELETE FROM CalendarEventOccurrences WHERE eventID=?',
                    array($ev->getID()));

                $occurrence = new EventOccurrence(
                    $ev,
                    strtotime($repetition->getStartDate()),
                    strtotime($repetition->getEndDate()));
                $occurrence->save();

                $attributes = EventKey::getList();
                foreach ($attributes as $ak) {
                    $ak->saveAttributeForm($ev);
                }

            } elseif ($edit_type == 'local') {
                /** @var DateTime $datetime */
                $datetime = \Core::make('helper/form/date_time');
                $repetition = new EventRepetition();

                $start = $datetime->translate('pdOccurrenceStartDate');
                $end = $datetime->translate('pdOccurrenceEndDate');

                if (!$start || !$end) {
                    $e->add('A valid start date must be provided.');
                } else {
                    $repetition->setStartDate($start);
                    $repetition->setEndDate($end);
                    $repetition->setRepeatPeriod($repetition::REPEAT_NONE);
                    $repetition->save();

                    $ev = new \Concrete\Core\Calendar\Event\Event(
                        $this->request->request->get('name'),
                        $this->request->request->get('description'),
                        $repetition);

                    $ev->setCalendar($occurrence->getEvent()->getCalendar());
                    $ev->save();

                    $now = strtotime($repetition->getStartDate());
                    $ev->generateOccurrences($now, strtotime('+5 years', $now));

                    $occurrence->delete();
                }
            } elseif ($edit_type === 'forward') {
                $repetition->save();

                $ev = new \Concrete\Core\Calendar\Event\Event(
                    $this->request->request->get('name'),
                    $this->request->request->get('description'),
                    $repetition);

                $db = \Database::connection();
                $db->query(
                    'DELETE FROM CalendarEventOccurrences WHERE startTime>=? AND eventID=?',
                    array(
                        $occurrence->getStart(),
                        $occurrence->getEvent()->getID()));

                $ev->setCalendar($occurrence->getEvent()->getCalendar());
                $ev->save();

                $now = strtotime($repetition->getStartDate());
                $ev->generateOccurrences($now, strtotime('+5 years', $now));

                $occurrence->delete();
            } else {
                $repetition->save();
                /** @var \Concrete\Core\Calendar\Event\Event $ev */
                $ev = $occurrence->getEvent();
                $ev->setName($this->request->request->get('name'));
                $ev->setDescription($this->request->request->get('description'));
                $rep = $ev->getRepetition();
                $ev->setRepetition($repetition);
                $ev->save();

                $now = strtotime($repetition->getStartDate()) - 1;
                $db = \Database::connection();
                $db->query(
                    'DELETE FROM CalendarEventOccurrences WHERE startTime>=? AND eventID=?',
                    array(
                        $now,
                        $ev->getID()));

                $ev->generateOccurrences($now, strtotime('+5 years', $now));

                $attributes = EventKey::getList();
                foreach ($attributes as $ak) {
                    $ak->saveAttributeForm($ev);
                }
            }

            // Commenting this out until we can do ajax style calendar updating. In the meantime
            // we're just going to refresh to the date of the start of the event and call it good.
            //$occurrences = $ev->getOccurrences();
            //$r->setOccurrences($occurrences);
            //$r->setMessage(t('Event added successfully.'));
            $year = date('Y', strtotime($repetition->getStartDate()));
            $month = date('m', strtotime($repetition->getStartDate()));
            $r->setRedirectURL(
                \URL::to(
                    '/dashboard/calendar/events/',
                    'view',
                    $calendar->getID(),
                    $year,
                    $month,
                    'event_saved'
                ));
        }

        $r->outputJSON();
    }

    public function delete($occurrence_id)
    {
        if ($this->canAccess()) {
            $occurrence = EventOccurrence::getByID($occurrence_id);

            if ($occurrence) {
                /** @var \Concrete\Core\Calendar\Event\Event $event */
                $event = $occurrence->getEvent();

                $occurrence_list = new EventOccurrenceList();
                $occurrence_list->filterByEvent($event);
                foreach ($occurrence_list->getResults() as $occurrence) {
                    $occurrence->delete();
                }

                $event->delete();

                $r = new RedirectResponse(
                    \URL::to(
                        '/dashboard/calendar/events/',
                        'view',
                        $event->getCalendar()->getID(),
                        date('y', $occurrence->getStart()),
                        date('m', $occurrence->getEnd()),
                        'event_deleted'
                    ));
                $r->send();
            } else {
                $r = new RedirectResponse(
                    \URL::to(
                        '/dashboard/calendar/events/',
                        'view',
                        null,
                        null,
                        null,
                        'event_delete_failed'
                    ));
                $r->send();
            }
        }

    }

    public function delete_local($occurrence_id)
    {
        if ($this->canAccess()) {
            $occurrence = EventOccurrence::getByID($occurrence_id);

            if ($occurrence) {
                $occurrence->delete();

                $r = new RedirectResponse(
                    \URL::to(
                        '/dashboard/calendar/events/',
                        'view',
                        $occurrence->getEvent()->getCalendar()->getID(),
                        date('y', $occurrence->getStart()),
                        date('m', $occurrence->getEnd()),
                        'event_occurrence_deleted'
                    ));
                $r->send();
            } else {
                $r = new RedirectResponse(
                    \URL::to(
                        '/dashboard/calendar/events/',
                        'view',
                        null,
                        null,
                        null,
                        'event_delete_failed'
                    ));
                $r->send();
            }
        }

    }

    public function submit($caID)
    {
        if ($this->canAccess()) {
            $repetition = EventRepetition::translateFromRequest($this->request);
            $e = \Core::make('error');
            if (!is_object($repetition)) {
                $e->add(t('You must specify a valid date for this event.'));
            }

            $calendar = Calendar::getByID($caID);
            if (!is_object($calendar)) {
                $e->add(t('Invalid calendar.'));
            }

            $r = new EditResponse($e);

            if (!$e->has()) {
                $repetition->save();
                $ev = new CalendarEvent(
                    $this->request->request->get('name'),
                    $this->request->request->get('description'),
                    $repetition
                );

                $repetition_start = strtotime($repetition->getStartDate());

                $ev->setCalendar($calendar);
                $ev->save();

                $ev->generateOccurrences($repetition_start - 1, strtotime('+5 years', $repetition_start));

                $attributes = EventKey::getList();
                foreach ($attributes as $ak) {
                    $ak->saveAttributeForm($ev);
                }

                // Commenting this out until we can do ajax style calendar updating. In the meantime
                // we're just going to refresh to the date of the start of the event and call it good.
                //$occurrences = $ev->getOccurrences();
                //$r->setOccurrences($occurrences);
                //$r->setMessage(t('Event added successfully.'));
                $year = date('Y', strtotime($repetition->getStartDate()));
                $month = date('m', strtotime($repetition->getStartDate()));
                $r->setRedirectURL(
                    \URL::to(
                        '/dashboard/calendar/events/',
                        'view',
                        $calendar->getID(),
                        $year,
                        $month,
                        'event_added'
                    ));
            }

            $r->outputJSON();
        }
    }

}

