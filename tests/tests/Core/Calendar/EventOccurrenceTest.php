<?php


class EventOccurrenceTest extends \ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array('Calendars', 'CalendarEventRepetitions', 'CalendarEvents', 'CalendarEventOccurrences');

    public function testSingleOccurrence()
    {
        $repetition = new \Concrete\Core\Calendar\Event\EventRepetition();
        $repetition->setStartDate('1979-07-11 9:00:00');
        $repetition->save();
        $ev = new \Concrete\Core\Calendar\Event\Event('Test Event', 'Test Description', $repetition);
        $ev->save();


        $repetition = new \Concrete\Core\Calendar\Event\EventRepetition();
        $repetition->setStartDate('2014-01-11 9:00:00');
        $repetition->save();
        $ev2 = new \Concrete\Core\Calendar\Event\Event('Test Event 2', 'Test Description 2', $repetition);
        $ev2->save();

        $list = $ev2->getOccurrenceList();
        $occurrences = $list->getResults();
        $this->assertEquals(1, count($occurrences));

        $occurrence = $occurrences[0];
        $this->assertEquals(2, $occurrence->getID());
        $this->assertEquals('2014-01-11 09:00:00', date('Y-m-d H:i:s', $occurrence->getStart()));
    }

    public function testSaveEventFromRequest()
    {

    }

}
