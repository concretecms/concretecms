<?php

declare(strict_types=1);

namespace Concrete\Tests\Calendar\Event;

use Concrete\Core\Attribute\Category\EventCategory;
use Concrete\Core\Calendar\Event\EventOccurrenceFactory;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\NullLogger;

class EventServiceTest extends TestCase
{
    public static function relatedPageProvider(): array
    {
        return [
            // original calendar mode, original page relation, original page ID, target calendar mode, target calendar page ID, expected page relation, expected page ID
            'page created for the event' => ['C', 'C', 42, null, null, null, 0],
            'page picked for the event' => [null, null, 42, null, null, null, 0],
            'no page' => [null, null, 0, null, null, null, 0],
            'page associated at the calendar level' => ['A', 'A', 7, null, null, 'A', 7],
            'copy to a calendar with another associated page' => ['A', 'A', 7, 'A', 9, 'A', 9],
            'copy to a calendar that creates pages' => ['A', 'A', 7, 'C', null, null, 0],
            'copy of a page created for the event to a calendar with an associated page' => ['C', 'C', 42, 'A', 9, 'A', 9],
        ];
    }

    /**
     * @dataProvider relatedPageProvider
     */
    public function testDuplicateDoesNotKeepTheEventPageOfTheOriginal(?string $calendarMode, ?string $relationType, int $pageID, ?string $targetCalendarMode, ?int $targetCalendarPageID, ?string $expectedRelationType, int $expectedPageID): void
    {
        $calendar = new Calendar();
        $calendar->setEnableMoreDetails($calendarMode);
        if ($calendarMode === 'A') {
            $calendar->setEventPageAssociatedID($pageID);
        }
        $targetCalendar = null;
        if ($targetCalendarMode !== null) {
            $targetCalendar = new Calendar();
            $targetCalendar->setEnableMoreDetails($targetCalendarMode);
            $targetCalendar->setEventPageAssociatedID($targetCalendarPageID);
        }
        $original = new CalendarEvent($calendar);
        $originalVersion = new CalendarEventVersion($original, $this->createMock(UserEntity::class));
        $originalVersion->setEvent($original);
        $originalVersion->setName('Board Meeting');
        $originalVersion->setPageID($pageID);
        $originalVersion->setRelatedPageRelationType($relationType);
        self::setNonPublicPropertyValue($originalVersion, 'eventVersionID', 10);
        self::setNonPublicPropertyValue($original, 'versions', new ArrayCollection([$originalVersion]));

        $persistedVersions = [];
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('persist')->willReturnCallback(static function ($entity) use (&$persistedVersions) {
            if ($entity instanceof CalendarEventVersion) {
                $persistedVersions[] = $entity;
            }
        });

        $new = $this->createService($entityManager)->duplicate($original, $this->createUser(), $targetCalendar);

        $this->assertCount(1, $persistedVersions);
        $newVersion = $persistedVersions[0];
        $this->assertSame($new, $newVersion->getEvent());
        $this->assertSame($targetCalendar ?? $calendar, $new->getCalendar());
        $this->assertNotSame($originalVersion, $newVersion);
        $this->assertSame('Board Meeting ' . t('Copy'), $newVersion->getName());
        $this->assertFalse($newVersion->isApproved());
        $this->assertSame($expectedPageID, $newVersion->getPageID());
        $this->assertSame($expectedRelationType, $newVersion->getRelatedPageRelationType());

        // The original event must keep its page.
        $this->assertSame($pageID, $originalVersion->getPageID());
        $this->assertSame($relationType, $originalVersion->getRelatedPageRelationType());
    }

    private function createService(EntityManagerInterface $entityManager): EventService
    {
        $eventCategory = $this->createMock(EventCategory::class);
        $eventCategory->method('getAttributeValues')->willReturn([]);
        $service = $this->getMockBuilder(EventService::class)
            ->setConstructorArgs([
                $entityManager,
                $this->createMock(Repository::class),
                $this->createMock(EventOccurrenceFactory::class),
                $eventCategory,
                $this->createMock(EventDispatcher::class),
            ])
            ->onlyMethods(['generateDefaultOccurrences'])
            ->getMock()
        ;
        $service->setLogger(new NullLogger());

        return $service;
    }

    private function createUser(): User
    {
        $userInfo = $this->createMock(UserInfo::class);
        $userInfo->method('getEntityObject')->willReturn($this->createMock(UserEntity::class));
        $user = $this->createMock(User::class);
        $user->method('getUserInfoObject')->willReturn($userInfo);

        return $user;
    }
}
