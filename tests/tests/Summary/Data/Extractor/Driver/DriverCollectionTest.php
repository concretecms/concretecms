<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicCalendarEventDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\CalendarEventThumbnailDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\PageThumbnailDriver;
use Concrete\Core\Summary\Data\Field\DataFieldData;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicPageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverCollection;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;
use Concrete\Tests\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
class DriverCollectionTest extends TestCase
{
    
    use MockeryPHPUnitIntegration;
    
    public function testExtractData()
    {
        $driverCollection = new DriverCollection();
        $page = M::Mock(Page::class);
        $date = M::mock(\DateTime::class);
        $date->shouldReceive("getTimestamp");
        $page->shouldReceive('getCollectionName')->andReturn('hi');
        $page->shouldReceive('getCollectionLink')->andReturn('https://foo.com');
        $page->shouldReceive('getCollectionDescription')->andReturn('');
        $page->shouldReceive('getCollectionDatePublicObject')->andReturn($date);
        $driver = M::mock(BasicPageDriver::class)->makePartial();
        $driverCollection->addDriver($driver);
        $driverCollection->addDriver($driver); // let's make sure we don't add multiple fields to the array
        $collection = $driverCollection->extractData($page);
        $this->assertCount(3, $collection->getFields());
        $field = $collection->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('hi', $field);
    }

    public function testExtractDataWithThumbnail()
    {
        $driverCollection = new DriverCollection();
        $page = M::Mock(Page::class);
        $date = M::mock(\DateTime::class);
        $date->shouldReceive("getTimestamp");
        $file = M::Mock(File::class);
        $file->shouldReceive('getFileID')->andReturn(3);
        $page->shouldReceive('getCollectionName')->once()->andReturn('hi');
        $page->shouldReceive('getCollectionLink')->once()->andReturn('https://foo.com');
        $page->shouldReceive('getCollectionDescription')->once()->andReturn('asd');
        $page->shouldReceive('getCollectionDatePublicObject')->andReturn($date);
        $page->shouldReceive('getAttribute')->with('thumbnail')->once()->andReturn($file);
        $driver = M::mock(BasicPageDriver::class)->makePartial();
        $driver2 = M::mock(PageThumbnailDriver::class)->makePartial();
        $driverCollection->addDriver($driver);
        $driverCollection->addDriver($driver2);
        $collection = $driverCollection->extractData($page);
        $this->assertCount(5, $collection->getFields());
        $field = $collection->getField(FieldInterface::FIELD_DESCRIPTION);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('asd', $field);
        $field = $collection->getField(FieldInterface::FIELD_THUMBNAIL);
        $this->assertInstanceOf(ImageDataFieldData::class, $field);
    }

    public function testExtractCalendarEventData()
    {
        $driverCollection = new DriverCollection();
        $event = M::Mock(CalendarEvent::class);
        $file = M::Mock(File::class);
        $eventVersion = M::mock(CalendarEventVersion::class);
        $linkFormatter = M::mock(LinkFormatter::class);
        $file->shouldReceive('getFileID')->andReturn(3);
        
        $occurrence = M::mock(CalendarEventOccurrence::class);
        $occurrence->shouldReceive('getStart')->andReturn(time());
        
        $linkFormatter->shouldReceive('getEventFrontendViewLink')->andReturn('https://foo.com/calendar/123');
        $event->shouldReceive('getApprovedVersion')->andReturn($eventVersion);
        $eventVersion->shouldReceive('getName')->andReturn('testtitle');
        $eventVersion->shouldReceive('getDescription')->andReturn('FOOOO');

        $event->shouldReceive('getAttribute')->with('event_thumbnail')->once()->andReturn($file);
        $eventVersion->shouldReceive('getOccurrences')->andReturn([$occurrence]);
        
        $driver1 = new BasicCalendarEventDriver($linkFormatter);
        $driver2 = M::mock(CalendarEventThumbnailDriver::class)->makePartial();
        $driverCollection->addDriver($driver1);
        $driverCollection->addDriver($driver2);

        $collection = $driverCollection->extractData($event);
        $this->assertCount(5, $collection->getFields());
        $fields = $collection->getFields();
        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('date', $fields);
        $this->assertArrayHasKey('description', $fields);
        $this->assertArrayHasKey('link', $fields);
        $this->assertArrayHasKey('thumbnail', $fields);
        $field = $collection->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('testtitle', $field);
    }
    
}
