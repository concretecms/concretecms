<?
namespace Concrete\Controller\Dialog\Calendar;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Calendar\Calendar;

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

    }

}

