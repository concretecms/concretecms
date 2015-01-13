<?php
namespace tests\Core\Calendar;

use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventOccurrenceFactory;
use Concrete\Core\Calendar\Event\EventRepetition;

class EventOccurrenceFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateDaily()
    {
        $repetition = new EventRepetition();

        // Every 2 days
        $repetition->setRepeatPeriod($repetition::REPEAT_DAILY);
        $repetition->setRepeatEveryNum(3);
        $repetition->setStartDate('12/10/1992 1:00:00');
        $repetition->setEndDate('12/11/1992 1:00:00');

        $event = new Event('Test Event', 'Test Event', $repetition);

        $now = time();
        $end = strtotime('+5 years', $now);

        $factory = new EventOccurrenceFactory();
        $occurrences = $factory->eventOccurrencesBetween($event, $now, $end);

        $all_active = true;
        foreach ($occurrences as $occurrence) {
            $window = $repetition->getActiveRange($occurrence->getStart());
            if (!$window) {
                $all_active = false;
                break;
            }

            if ($window[0] !== $occurrence->getStart() || $window[1] !== $occurrence->getEnd()) {
                $all_active = false;
                break;
            }
        }

        $this->assertTrue($all_active, 'EventOccurrenceFactory generated inactive occurrences.');
    }

    public function testGenerateWeekly()
    {
        $repetition = new EventRepetition();

        // Every 2 days
        $repetition->setRepeatPeriod($repetition::REPEAT_WEEKLY);
        $repetition->setRepeatEveryNum(3);
        $repetition->setStartDate('1/1/2015 01:00:00');
        $repetition->setEndDate('1/1/2015 03:00:00');

        // Sunday, Tuesday
        $repetition->setRepeatPeriodWeekDays(array(2, 3, 0));
        $event = new Event('Test Event', 'Test Event', $repetition);

        $now = time();
        $end = strtotime('+5 years', $now);

        $factory = new EventOccurrenceFactory();
        $occurrences = $factory->eventOccurrencesBetween($event, $now, $end);

        $all_active = true;
        foreach ($occurrences as $occurrence) {
            $window = $repetition->getActiveRange($occurrence->getStart());
            if (!$window) {
                $all_active = false;
                break;
            }

            if ($window[0] !== $occurrence->getStart() || $window[1] !== $occurrence->getEnd()) {
                $all_active = false;
                break;
            }
        }

        $this->assertTrue($all_active, 'EventOccurrenceFactory generated inactive occurrences.');
    }

}
