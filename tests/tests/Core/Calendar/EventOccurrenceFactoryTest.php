<?php
namespace tests\Core\Calendar;

use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventOccurrenceFactory;
use Concrete\Core\Calendar\Event\EventRepetition;

class EventOccurrenceFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $repetition = new EventRepetition();
        $event = new Event('name', 'description', $repetition);

        $now = time();
        $end = strtotime('+3 days', $now);

        $factory = new EventOccurrenceFactory();
        $occurrence = $factory->createEventOccurrence($event, $now, $end);

        $this->assertInstanceOf('\Concrete\Core\Calendar\Event\EventOccurrence', $occurrence);
        $this->assertEquals($now, $occurrence->getStart());
        $this->assertEquals($end, $occurrence->getEnd());
    }

}
