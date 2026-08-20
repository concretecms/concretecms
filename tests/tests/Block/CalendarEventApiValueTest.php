<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the calendar event block.
 *
 * @see \Concrete\Block\CalendarEvent\Controller::getApiValueSchema()
 * @see \Concrete\Block\CalendarEvent\Controller::serializeValueForApi()
 * @see \Concrete\Block\CalendarEvent\Controller::getImportDataFromApiValue()
 */
class CalendarEventApiValueTest extends BlockApiValueTestCase
{
    /**
     * The ID of the event displayed by the block (this block type only stores it).
     *
     * @var int
     */
    private const EVENT_ID = 4321;

    /**
     * The calendar the event belongs to.
     *
     * @var \Concrete\Core\Entity\Calendar\Calendar|null
     */
    private $calendar;

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
        $this->calendar = null;
    }

    public function testTheAttributesToBeDisplayedAreExchangedAsAList(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['displayEventAttributes' => [12, 34]]);

        $value = $this->getApiValue($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame([12, 34], $value['displayEventAttributes']);
        // ... while the database (and so the CIF representation) holds them as a JSON document
        $db = $this->app->make(Connection::class);
        static::assertSame(
            '[12,34]',
            $db->fetchOne('select displayEventAttributes from btCalendarEvent where bID = ?', [$block->getBlockID()])
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'calendar_event';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        return [
            'mode' => 'S',
            'calendarEventAttributeKeyHandle' => '',
            'calendarID' => $this->getCalendar()->getID(),
            'eventID' => self::EVENT_ID,
            'displayEventAttributes' => [],
            'allowExport' => 1,
            'enableLinkToPage' => 0,
            'displayEventName' => 1,
            'displayEventDate' => 0,
            'displayEventDescription' => 1,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btCalendarEvent table
        return [
            'mode' => 'S',
            'calendarEventAttributeKeyHandle' => '',
            'calendarID' => (string) $this->getCalendar()->getID(),
            'eventID' => (string) self::EVENT_ID,
            'displayEventAttributes' => [],
            'allowExport' => '1',
            'enableLinkToPage' => '0',
            'displayEventName' => '1',
            'displayEventDate' => '0',
            'displayEventDescription' => '1',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['displayEventName' => '0'];
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
     * Get the calendar the event belongs to (it's created the first time it's asked for).
     */
    private function getCalendar(): Calendar
    {
        if ($this->calendar === null) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $calendar = new Calendar();
            $calendar->setName('The calendar of the tests');
            $entityManager->persist($calendar);
            $entityManager->flush();
            $this->calendar = $calendar;
        }

        return $this->calendar;
    }
}
