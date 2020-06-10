<?php
namespace Concrete\Core\Board\DataSource\Saver;

use Concrete\Core\Calendar\Utility\Preferences;
use Concrete\Core\Entity\Board\DataSource\Configuration\CalendarEventConfiguration;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Search\Field\AttributeKeyField;
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
        $preferences = app(Preferences::class);
        $topicsKey = $preferences->getCalendarTopicsAttributeKey();
        if ($topicsKey) {
            $treeNodeID = $topicsKey->getController()->request('treeNodeID');
            if ($treeNodeID) {
                $calendarEventConfiguration->setTopicTreeNodeID($treeNodeID);
            }
        }
        return $calendarEventConfiguration;
    }


}
