<?
namespace Concrete\Controller\Dialog\Calendar;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EventOccurrence;
use Concrete\Core\Calendar\Event\EventOccurrenceList;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Calendar\Event\Event as CalendarEvent;
use RedirectResponse;

class Event extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/calendar/event/form';

    protected function canAccess()
    {
        $c = \Page::getByPath('/dashboard/calendar/events');
        $cp = new \Permissions($c);
        return $cp->canViewPage();
    }

    public function add($caID)
    {
        $calendar = Calendar::getByID($caID);
        if (!is_object($calendar)) {
            throw new \Exception(t('Invalid calendar.'));
        }
    }

    public function edit($occurrence_id=0)
    {
        $occurrence = EventOccurrence::getByID($occurrence_id);
        if (!$occurrence) {
            throw new \Exception(t('Invalid occurrence.'));
        }

        $this->set('occurrence', $occurrence);
    }

    public function save($occurrence_id)
    {
        $repetition = EventRepetition::translateFromRequest($this->request);
        $e = \Core::make('error');
        if (!is_object($repetition)) {
            $e->add(t('You must specify a valid date for this event.'));
        }

        $occurrence = EventOccurrence::getByID($occurrence_id);
        if (!$occurrence) {
            throw new \Exception(t('Invalid occurrence.'));
        }

        $r = new EditResponse($e);

        if (!$e->has()) {
            $repetition->save();
            $ev = $occurrence->getEvent();
            $ev->setName($this->request->request->get('name'));
            $ev->setDescription($this->request->request->get('description'));
            $rep = $ev->getRepetition();
            $ev->setRepetition($repetition);
            $ev->save();

            $attributes = EventKey::getList();
            foreach($attributes as $ak) {
                $ak->saveAttributeForm($ev);
            }

            // Commenting this out until we can do ajax style calendar updating. In the meantime
            // we're just going to refresh to the date of the start of the event and call it good.
            //$occurrences = $ev->getOccurrences();
            //$r->setOccurrences($occurrences);
            //$r->setMessage(t('Event added successfully.'));
            $year = date('Y', strtotime($repetition->getStartDate()));
            $month = date('m', strtotime($repetition->getStartDate()));
            $r->setRedirectURL(\URL::to('/dashboard/calendar/events/', 'view', $ev->getCalendar()->getID(),
                                        $year, $month, 'event_saved'
            ));
        }

        $r->outputJSON();
    }

    public function delete($occurrence_id)
    {
        $occurrence = EventOccurrence::getByID($occurrence_id);

        if ($occurrence) {
            /** @var \Concrete\Core\Calendar\Event\Event $event */
            $event = $occurrence->getEvent();
            $event->delete();

            $occurrence_list = new EventOccurrenceList();
            $occurrence_list->filterByEvent($event);
            foreach($occurrence_list->getResults() as $occurrence_row) {
                $occurrence_list->getResult($occurrence_row)->delete();
            }
            $r = new RedirectResponse(\URL::to('/dashboard/calendar/events/', 'view', $event->getCalendar()->getID(),
                                        null, null, 'event_deleted'
            ));
            $r->send();
        } else {
            $r = new RedirectResponse(\URL::to('/dashboard/calendar/events/', 'view', null,
                                        null, null, 'event_delete_failed'
            ));
            $r->send();
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

                $ev->setCalendar($calendar);
                $ev->save();

                $attributes = EventKey::getList();
                foreach($attributes as $ak) {
                    $ak->saveAttributeForm($ev);
                }

                // Commenting this out until we can do ajax style calendar updating. In the meantime
                // we're just going to refresh to the date of the start of the event and call it good.
                //$occurrences = $ev->getOccurrences();
                //$r->setOccurrences($occurrences);
                //$r->setMessage(t('Event added successfully.'));
                $year = date('Y', strtotime($repetition->getStartDate()));
                $month = date('m', strtotime($repetition->getStartDate()));
                $r->setRedirectURL(\URL::to('/dashboard/calendar/events/', 'view', $calendar->getID(),
                    $year, $month, 'event_added'
                ));
            }

            $r->outputJSON();
        }
    }

}

