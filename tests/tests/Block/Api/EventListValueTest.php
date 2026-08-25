<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the event list block.
 *
 * @see \Concrete\Block\EventList\Controller::getApiValueSchema()
 * @see \Concrete\Block\EventList\Controller::serializeValueForApi()
 * @see \Concrete\Block\EventList\Controller::getImportDataFromApiValue()
 */
class EventListValueTest extends BlockApiValueTestCase
{
    /**
     * The calendars created by the tests, by their name.
     *
     * @var \Concrete\Core\Entity\Calendar\Calendar[]
     */
    private $calendars = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            Calendar::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->calendars = [];
    }

    public function testTheCalendarsAreExchangedAsAListOfIDs(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'caID' => [$this->getCalendarID('Events'), $this->getCalendarID('More events')],
        ]);

        static::assertSame(
            [$this->getCalendarID('Events'), $this->getCalendarID('More events')],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['caID']
        );
        // ... while the database (and so the CIF representation) holds them as a JSON document
        $db = $this->app->make(Connection::class);
        static::assertSame(
            '[' . $this->getCalendarID('Events') . ',' . $this->getCalendarID('More events') . ']',
            $db->fetchOne('select caID from btEventList where bID = ?', [$block->getBlockID()])
        );
    }

    public function testTheCalendarOfTheSiteIsUsedWhenNoCalendarIsSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'caID' => [],
            'calendarAttributeKeyHandle' => 'site_calendar',
        ]);

        $value = $this->getApiValue($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame([], $value['caID']);
        static::assertSame('site_calendar', $value['calendarAttributeKeyHandle']);
    }

    public function testThePageOfTheCalendarIsExchangedAsAReference(): void
    {
        $block = $this->addBlock();
        $page = $block->getBlockCollectionObject();

        $this->updateBlock($block, [
            'linkToPage' => '{ccm:export:page::id=' . $page->getCollectionID() . '}',
        ]);

        static::assertSame(
            '{ccm:export:page::id=' . $page->getCollectionID() . '}',
            $this->getApiValue($this->getBlock($page))['linkToPage']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'event_list';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends
        return [
            'chooseCalendar' => 'specific',
            'caID' => [$this->getCalendarID('Events')],
            'calendarAttributeKeyHandle' => '',
            'eventPeriod' => 'past_events',
            'eventOrder' => 'oldest_first',
            'filterByFeatured' => 1,
            'filterByTopic' => 'none',
            'totalToRetrieve' => 70,
            'totalPerPage' => 9,
            'eventListTitle' => 'Great Events',
            'titleFormat' => 'h3',
            'linkToPage' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btEventList table
        return [
            'caID' => [$this->getCalendarID('Events')],
            'calendarAttributeKeyHandle' => '',
            'totalToRetrieve' => '70',
            'totalPerPage' => '9',
            'filterByTopicAttributeKeyID' => '0',
            'filterByTopicID' => '0',
            'filterByPageTopicAttributeKeyHandle' => '',
            'filterByFeatured' => '1',
            'eventListTitle' => 'Great Events',
            'linkToPage' => '0',
            'titleFormat' => 'h3',
            'eventPeriod' => 'past_events',
            'eventOrder' => 'oldest_first',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['eventListTitle' => 'Other events'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::hasCustomApiValue()
     */
    protected function hasCustomApiValue(): bool
    {
        return true;
    }

    /**
     * Get the ID of a calendar (it's created the first time it's asked for).
     */
    private function getCalendarID(string $name): int
    {
        if (!isset($this->calendars[$name])) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $calendar = new Calendar();
            $calendar->setName($name);
            $entityManager->persist($calendar);
            $entityManager->flush();
            $this->calendars[$name] = $calendar;
        }

        return (int) $this->calendars[$name]->getID();
    }
}
