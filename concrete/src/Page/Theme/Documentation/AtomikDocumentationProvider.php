<?php

namespace Concrete\Core\Page\Theme\Documentation;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Calendar\Event\EventList;
use Concrete\Core\Calendar\Event\EventOccurrenceFactory;
use Concrete\Core\Calendar\Event\EventRepetition;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Entity\Calendar\Calendar as CalendarEntity;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventRepetition;
use Concrete\Core\Entity\Calendar\CalendarEventVersionRepetition;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\ObjectManager;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Foundation\Repetition\BasicRepetition;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\User\User;
use Concrete\Theme\Atomik\PageTheme;
use Doctrine\ORM\EntityManager;

class AtomikDocumentationProvider implements DocumentationProviderInterface
{

    /**
     * @var PageTheme
     */
    protected $theme;

    public function __construct(PageTheme $theme)
    {
        $this->theme = $theme;
    }

    public function clearSupportingElements(): void
    {
        $em = app(EntityManager::class);
        $expressObject = $em->getRepository(Entity::class)->findOneByHandle('atomik_employee');
        if ($expressObject) {
            $em->remove($expressObject);
            $em->flush();
        }

        $importer = new ContentImporter();
        $documentation = FileFolder::getNodeByName('Atomik Documentation');
        if ($documentation) {
            $documentation->delete();
        }

        $importer->deleteFilesByName(
            [
                'atomik-documentation-image-wide-01.jpg',
                'atomik-documentation-image-tall-01.jpg',
                'atomik-documentation-video.mp4',
                'atomik-documentation-image-logo-01.png',
                'atomik-documentation-image-logo-02.png',
                'atomik-documentation-image-logo-03.png',
                'atomik-documentation-image-logo-04.png',
            ]
        );

        $calendar = $em->getRepository(CalendarEntity::class)->findOneByName('Atomik Sample Calendar');
        if ($calendar) {
            $em->remove($calendar);
            $em->flush();
        }
    }

    public function installSupportingElements(): void
    {
        $em = app(EntityManager::class);
        $cache = app('cache/request');
        $cache->disable();

        $importer = new ContentImporter();
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $filesystem->addFolder($root, 'Atomik Documentation');

        $importer->importContentFile(
            $this->theme->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEME_DOCUMENTATION .
            DIRECTORY_SEPARATOR .
            'support' .
            DIRECTORY_SEPARATOR .
            'forms.xml'
        );

        // We need this because if you haven't installed Atomik Full (i.e. you've installed Elemental instead)
        // You're not gonna have these containers in your site.
        $importer->importContentFile(
            $this->theme->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEME_DOCUMENTATION .
            DIRECTORY_SEPARATOR .
            'support' .
            DIRECTORY_SEPARATOR .
            'containers.xml'
        );


        $importer->importFiles(
            $this->theme->getThemeDirectory() .
            DIRECTORY_SEPARATOR .
            DIRNAME_THEME_DOCUMENTATION .
            DIRECTORY_SEPARATOR .
            'files'
        );

        $importer->moveFilesByName(
            [
                'atomik-documentation-image-wide-01.jpg',
                'atomik-documentation-image-tall-01.jpg',
                'atomik-documentation-video.mp4',
                'atomik-documentation-image-logo-01.png',
                'atomik-documentation-image-logo-02.png',
                'atomik-documentation-image-logo-03.png',
                'atomik-documentation-image-logo-04.png',
            ],
            'Atomik Documentation'
        );

        // Express Data
        $employeeData = [
            ['Sam', 'Heinz', '2020-09-01 00:00:00', 'sam.heinz@example.com', '503-555-5555'],
            ['Ruthie', 'Rich', '2019-04-23 00:00:00', 'ruthie.rich@example.com', '503-555-5555'],
            ['Amanda', 'Johnson', '2021-03-03 00:00:00', 'amanda.johnson@example.com', '503-555-5555'],
        ];

        $express = app(ObjectManager::class);
        $object = $express->getObjectByHandle('atomik_employee');
        $express->refresh($object);

        for ($i = 0; $i < count($employeeData); $i++) {
            $employeeEntryData = $employeeData[$i];
            $express->buildEntry('atomik_employee')
                ->setEmployeeFirstName($employeeEntryData[0])
                ->setEmployeeLastName($employeeEntryData[1])
                ->setEmployeeStartDate($employeeEntryData[2])
                ->setEmployeeEmailAddress($employeeEntryData[3])
                ->setEmployeePhoneNumber($employeeEntryData[4])
                ->save();
        }

        // Install Calendar
        $site = app('site')->getSite();
        $calendar = new CalendarEntity();
        $calendar->setSite($site);
        $calendar->setName('Atomik Sample Calendar');
        $em->persist($calendar);
        $em->flush();

        $eventService = app(EventService::class);
        $occurrenceFactory = app(EventOccurrenceFactory::class);
        $events = [
            ['Manager Meeting', 'Next Wednesday 9:00am', true],
            ['Game Night', 'Next Saturday 6:00pm'],
            ['Company Lunch', 'Next Tuesday 12:00pm', true],
        ];
        $user = User::getByUserID(USER_SUPER_ID);
        foreach ($events as $eventData) {
            $event = new CalendarEvent($calendar);
            $eventVersion = $eventService->getVersionToModify($event, $user);
            $eventVersion->setName($eventData[0]);
            $eventVersion->setIsApproved(true);
            $date = new \DateTime();
            $date->modify($eventData[1]);
            $repetition = new EventRepetition();
            $repetition->setStartDate($date->format('Y-m-d H:i:s'));
            if (isset($eventData[2]) && $eventData[2]) {
                $repetition->setRepeatPeriod(BasicRepetition::REPEAT_WEEKLY);
                $repetition->setRepeatEveryNum(1);
            }
            $repetitionEntity = new CalendarEventRepetition($repetition);
            $calendarEventVersionRepetitionEntity = new CalendarEventVersionRepetition(
                $eventVersion, $repetitionEntity
            );
            $eventVersionRepetitions = [$calendarEventVersionRepetitionEntity];
            $eventService->addEventVersion($event, $calendar, $eventVersion, $eventVersionRepetitions);
            $start = $repetition->getStartDateTimestamp() - 1;
            $datetime = new \DateTime('+1 years', $repetition->getTimezone());
            $end = $datetime->getTimestamp();
            $occurrenceFactory->generateOccurrences($eventVersion, $calendarEventVersionRepetitionEntity, $start, $end);
        }
    }

    public function finishInstallation(): void
    {
        $express = app(ObjectManager::class);
        $em = app(EntityManager::class);
        foreach ($this->theme->getThemeDocumentationPages() as $page) {
            if ($page->getCollectionHandle() == 'forms-express') {
                $blocks = $page->getBlocks('Main');
                foreach ($blocks as $block) {
                    if ($block->getBlockTypeHandle() == 'express_entry_detail') {
                        $object = $express->getObjectByHandle('atomik_employee');
                        if ($object) {
                            $list = new EntryList($object);
                            $list->setItemsPerPage(1);
                            $results = $list->getResults();
                            if (!empty($results[0])) {
                                $block->update(['exSpecificEntryID' => $results[0]->getId()]);
                                $block->refreshBlockRecordCache();
                            }
                        }
                    }
                }
            }
            $calendar = $em->getRepository(CalendarEntity::class)->findOneByName('Atomik Sample Calendar');
            if ($calendar) {
                if ($page->getCollectionHandle() == 'calendar-events') {
                    $blocks = $page->getBlocks('Main');
                    foreach ($blocks as $block) {
                        if ($block->getBlockTypeHandle() == 'calendar' || $block->getBlockTypeHandle(
                            ) == 'event_list') {
                            $block->update(['caID' => $calendar->getID()]);
                            $block->refreshBlockRecordCache();
                        } else {
                            if ($block->getBlockTypeHandle() == 'calendar_event') {
                                $eventList = new EventList();
                                $results = $eventList->getResults();
                                if (isset($results[0])) {
                                    $block->update(
                                        [
                                            'calendarID' => $calendar->getID(),
                                            'eventID' => $results[0]->getID(),
                                            'allowExport' => 1,
                                            'displayEventName' => 1,
                                            'displayEventDate' => 1,
                                            'displayEventDescription' => 1
                                        ]
                                    );
                                    $block->refreshBlockRecordCache();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return DocumentationPageInterface[]
     */
    public function getPages(): array
    {
        $pages = [
            new ThemeDocumentationPage($this->theme, 'Overview', 'overview.xml'),
            new ThemeDocumentationPage($this->theme, 'Colors', 'colors.xml'),
            new BedrockDocumentationPage('Typography', 'typography.xml'),
            new BedrockDocumentationPage('Components', 'components.xml'),
            new ThemeDocumentationPage($this->theme, 'Containers', 'containers.xml'),
            new ThemeCategoryDocumentationPage($this->theme, 'Block Types', [
                new ThemeDocumentationPage($this->theme, 'Basics', 'basics.xml'),
                new ThemeDocumentationPage($this->theme, 'Navigation', 'navigation.xml'),
                new ThemeDocumentationPage($this->theme, 'Forms & Express', 'forms.xml'),
                new ThemeDocumentationPage($this->theme, 'Social Networking', 'social.xml'),
                new ThemeDocumentationPage($this->theme, 'Calendar & Events', 'calendar.xml'),
                new ThemeDocumentationPage($this->theme, 'Multimedia', 'multimedia.xml')
            ])
        ];

        return $pages;
    }


}
