<?php

namespace Concrete\Tests\Summary\Data\Extractor\Driver;

use Concrete\Core\Calendar\Event\Formatter\LinkFormatter;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicCalendarEventDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\CalendarEventThumbnailDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\PageThumbnailDriver;
use Concrete\Core\Summary\Data\Field\DataFieldData;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicPageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverCollection;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;
use Concrete\Core\User\Avatar\AvatarInterface;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManager;
use Mockery as M;
class DriverCollectionTest extends TestCase
{


    public function testExtractData()
    {
        $driverCollection = new DriverCollection();
        $page = M::Mock(Page::class);
        $date = M::mock(\DateTime::class);
        $date->shouldReceive("getTimestamp");
        $file = M::Mock(File::class);
        $file->shouldReceive('getFileID')->andReturn(3);
        $page->shouldReceive('getCollectionName')->once()->andReturn('hi');
        $page->shouldReceive('getCollectionPath')->once()->andReturn('/path/to/page');
        $page->shouldReceive('getCollectionDescription')->once()->andReturn('asd');
        $page->shouldReceive('getCollectionDatePublicObject')->andReturn($date);
        $page->shouldReceive('getAttribute')->with('thumbnail')->once()->andReturn($file);
        $page->shouldReceive('getCollectionUserID')->once();
        $installationService = M::mock(InstallationService::class);
        $userInfoRepository = M::mock(UserInfoRepository::class);
        $userInfo = M::mock(UserInfo::class);
        $mockAvatar = M::mock(AvatarInterface::class);
        $mockAvatar->shouldReceive('getPath');
        $userInfo->shouldReceive('getUserDisplayName')->andReturn('testuser');
        $userInfo->shouldReceive('getUserID');
        $userInfo->shouldReceive('getUserAvatar')->andReturn($mockAvatar);
        $installationService->shouldReceive('isMultisiteEnabled');
        $userInfoRepository->shouldReceive('getByID')->once()->andReturn($userInfo);
        $driver = M::mock(BasicPageDriver::class, [$installationService, $userInfoRepository])->makePartial();
        $driverCollection->addDriver($driver);
        $collection = $driverCollection->extractData($page);
        $this->assertCount(6, $collection->getFields());
        $field = $collection->getField(FieldInterface::FIELD_DESCRIPTION);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('asd', $field);
        $field = $collection->getField(FieldInterface::FIELD_THUMBNAIL);
        $this->assertInstanceOf(ImageDataFieldData::class, $field);
    }

    public function testExtractCalendarEventData()
    {
        $entityManager = M::mock(EntityManager::class);
        $entityManager->shouldReceive('refresh');

        $driverCollection = new DriverCollection();
        $event = M::Mock(CalendarEvent::class);
        $file = M::Mock(File::class);
        $repository = M::Mock(Repository::class);

        $eventVersion = M::mock(CalendarEventVersion::class);
        $linkFormatter = M::mock(LinkFormatter::class);
        $file->shouldReceive('getFileID')->andReturn(3);

        $calendar = M::mock(Calendar::class);
        $calendar->shouldReceive('getTimezone')->andReturn(null);
        $occurrence = M::mock(CalendarEventOccurrence::class);
        $occurrence->shouldReceive('getStart')->andReturn(time());
        $occurrence->shouldReceive('getEnd')->andReturn(time() + 500);

        $linkFormatter->shouldReceive('getEventFrontendViewLink')->andReturn('https://foo.com/calendar/123');
        $event->shouldReceive('getApprovedVersion')->andReturn($eventVersion);
        $event->shouldReceive('getCalendar')->andReturn($calendar);
        $eventVersion->shouldReceive('getName')->andReturn('testtitle');
        $eventVersion->shouldReceive('getDescription')->andReturn('FOOOO');

        $author = M::mock(User::class);
        $userInfo = M::mock(UserInfo::class);
        $mockAvatar = M::mock(AvatarInterface::class);
        $mockAvatar->shouldReceive('getPath');
        $author->shouldReceive('getUserInfoObject')->andReturn($userInfo);
        $userInfo->shouldReceive('getUserDisplayName')->andReturn('testuser');
        $userInfo->shouldReceive('getUserID');
        $userInfo->shouldReceive('getUserAvatar')->andReturn($mockAvatar);

        $event->shouldReceive('getAttribute')->with('event_thumbnail')->once()->andReturn($file);
        $event->shouldReceive('getAttribute')->with('event_category')->once()->andReturn(null);
        $eventVersion->shouldReceive('getOccurrences')->andReturn([$occurrence]);
        $eventVersion->shouldReceive('getAuthor')->andReturn($author);
        $repository->shouldReceive('get');

        $driver1 = new BasicCalendarEventDriver($entityManager, $linkFormatter, $repository);
        $driverCollection->addDriver($driver1);

        $collection = $driverCollection->extractData($event);
        $this->assertCount(8, $collection->getFields());
        $fields = $collection->getFields();
        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('date', $fields);
        $this->assertArrayHasKey('description', $fields);
        $this->assertArrayHasKey('link', $fields);
        $this->assertArrayHasKey('thumbnail', $fields);
        $this->assertArrayHasKey(FieldInterface::FIELD_DATE_END, $fields);
        $field = $collection->getField(FieldInterface::FIELD_TITLE);
        $this->assertInstanceOf(DataFieldData::class, $field);
        $this->assertEquals('testtitle', $field);
    }

}
