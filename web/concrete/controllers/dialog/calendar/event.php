<?
namespace Concrete\Controller\Dialog\Calendar;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Calendar\Event\EditResponse;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Calendar\Event\Event as CalendarEvent;

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

    public function submit($caID)
    {
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

