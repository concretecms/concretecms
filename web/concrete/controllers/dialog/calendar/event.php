<?
namespace Concrete\Controller\Dialog\Calendar;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
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

    public function submit()
    {
        $repetition = new EventRepetition();
        $repetition->setStartDate(date('Y-m-d'));
        $repetition->setStartDateAllDay(true);
        $repetition->setEndDate(date('Y-m-d'));
        $repetition->setEndDateAllDay(true);
        $repetition->save();
        $ev = new CalendarEvent(
            $this->request->request->get('name'),
            $this->request->request->get('description'),
            $repetition
        );
        $ev->save();

        $r = new EditResponse();
        $r->setMessage(t('Event added successfully.'));
        $r->outputJSON();
    }

}

