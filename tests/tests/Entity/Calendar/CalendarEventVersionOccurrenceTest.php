<?php

declare(strict_types=1);

namespace Concrete\Tests\Entity\Calendar;

use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventRepetition;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Entity\User\User;
use Concrete\Tests\TestCase;

class CalendarEventVersionOccurrenceTest extends TestCase
{
    public function testGetJSONObjectIncludesVersionPayload(): void
    {
        $calendar = new Calendar();
        $event = new CalendarEvent($calendar);
        $version = new CalendarEventVersion($event, $this->createMock(User::class));
        $version->setEvent($event);
        $version->setName('Board Meeting!');
        $version->setDescription('Quarterly planning session');

        self::setNonPublicPropertyValue($event, 'eventID', 123);
        self::setNonPublicPropertyValue($version, 'eventVersionID', 456);

        $repetitionObject = new EventRepetition();
        $repetitionObject->setRepeatPeriod(EventRepetition::REPEAT_NONE);
        $repetition = new CalendarEventRepetition($repetitionObject);

        $occurrence = new CalendarEventVersionOccurrence($version, $repetition, 1713139200, 1713142800);
        $json = $occurrence->getJSONObject();

        $this->assertSame(1713139200, $json->start);
        $this->assertSame(1713142800, $json->end);
        $this->assertSame(123, $json->id);
        $this->assertSame('Board Meeting!', $json->name);
        $this->assertSame(456, $json->versionId);
        $this->assertSame('Quarterly planning session', $json->description);
    }
}
