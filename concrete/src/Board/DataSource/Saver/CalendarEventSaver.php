<?php
namespace Concrete\Core\Board\DataSource\Saver;

use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Calendar\Calendar;
use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventSaver extends AbstractSaver
{

    public function createConfiguration(Request $request)
    {
        $calendarEventConfiguration = new CalendarEventConfiguration();
        $calendar = $this->entityManager->find(Calendar::class, $request->request->get('calendarID'));
        if ($calendar) {
            $calendarEventConfiguration->setCalendar($calendar);
        }
        return $calendarEventConfiguration;
    }


}
