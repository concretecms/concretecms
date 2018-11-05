<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Entity\Calendar;

class CalendarEventSelector
{
    public function selectEvent(Calendar $calendar, $fieldName, $eventID = false)
    {
        $v = \View::getInstance();
        $v->requireAsset('core/calendar/event-selector');

        $selectedEventID = 0;
        if (isset($_REQUEST[$fieldName])) {
            $selectedEventID = intval($_REQUEST[$fieldName]);
        } else {
            if ($eventID > 0) {
                $selectedEventID = $eventID;
            }
        }

        $calendarID = $calendar->getID();

        if ($selectedEventID) {
            $args = "{'inputName': '{$fieldName}', 'calendarID': {$calendarID}, 'eventID': {$selectedEventID}}";
        } else {
            $args = "{'inputName': '{$fieldName}', 'calendarID': {$calendarID}}";
        }

        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);
        $html = <<<EOL
        <div data-calendar-event-selector="{$identifier}"></div>
        <script type="text/javascript">
        $(function() {
            $('[data-calendar-event-selector={$identifier}]').concreteCalendarEventSelector({$args});
        });
        </script>
EOL;

        return $html;
    }
}
