<?php


class EventOccurrenceTest extends \ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array('CalendarEventRepetitions', 'CalendarEvents', 'CalendarEventOccurrences');

    public function testSingleOccurrence()
    {
        $repetition = new \Concrete\Core\Calendar\Event\EventRepetition();
        $repetition->setStartDate('1979-07-11 9:00:00');
        $repetition->save();
        $ev = new \Concrete\Core\Calendar\Event\Event('Test Event', 'Test Description', $repetition);
        $ev->save();

    }

}
