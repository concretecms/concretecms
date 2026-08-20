<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the calendar block.
 *
 * @see \Concrete\Block\Calendar\Controller::getApiValueSchema()
 * @see \Concrete\Block\Calendar\Controller::getImportDataFromApiValue()
 */
class CalendarApiValueTest extends BlockApiValueTestCase
{
    /**
     * The calendar displayed by the block.
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

    public function testTheDisplayedCalendarCanBeChanged(): void
    {
        $block = $this->addBlock();
        $anotherCalendar = $this->createCalendar('Another calendar');

        $this->updateBlock($block, ['caID' => (string) $anotherCalendar->getID()]);

        static::assertSame(
            (string) $anotherCalendar->getID(),
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['caID']
        );
    }

    public function testTheViewsAreExchangedAsLists(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['viewTypes' => ['listWeek'], 'viewTypesOrder' => ['listWeek_Week']]);

        $value = $this->getApiValue($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame(['listWeek'], $value['viewTypes']);
        static::assertSame(['listWeek_Week'], $value['viewTypesOrder']);
        // ... while the database (and so the CIF representation) holds them as JSON documents
        $db = $this->app->make(Connection::class);
        static::assertSame(
            '["listWeek"]',
            $db->fetchOne('select viewTypes from btCalendar where bID = ?', [$block->getBlockID()])
        );
    }

    public function testTheSchemaListsTheAvailableViews(): void
    {
        $block = $this->addBlock();

        $schema = $this->app->make(ApiValueSchemaFactory::class)->getSchema($block->getController());

        $viewTypes = ['month', 'basicWeek', 'basicDay', 'listYear', 'listMonth', 'listWeek', 'listDay'];
        static::assertSame('array', $schema['properties']['viewTypes']['type']);
        static::assertSame($viewTypes, $schema['properties']['viewTypes']['items']['enum']);
        // the default view is one of them, or nothing at all
        static::assertSame(array_merge([''], $viewTypes), $schema['properties']['defaultView']['enum']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'calendar';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends (the eventLimit checkbox is not checked)
        return [
            'chooseCalendar' => 'specific',
            'caID' => $this->getCalendar()->getID(),
            'calendarAttributeKeyHandle' => '',
            'viewTypes' => ['month', 'basicDay'],
            'viewTypesOrder' => ['month_Month', 'basicDay_Day'],
            'defaultView' => 'basicDay',
            'navLinks' => 1,
            'filterByTopicAttributeKeyID' => 0,
            'lightboxProperties' => ['linkToPage'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btCalendar table
        return [
            'caID' => (string) $this->getCalendar()->getID(),
            'calendarAttributeKeyHandle' => '',
            'filterByTopicAttributeKeyID' => '0',
            'filterByTopicID' => '0',
            'viewTypes' => ['month', 'basicDay'],
            'viewTypesOrder' => ['month_Month', 'basicDay_Day'],
            'defaultView' => 'basicDay',
            'navLinks' => '1',
            'eventLimit' => '0',
            'lightboxProperties' => ['linkToPage'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['defaultView' => 'month'];
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
     * Get the calendar displayed by the block (it's created the first time it's asked for).
     */
    private function getCalendar(): Calendar
    {
        if ($this->calendar === null) {
            $this->calendar = $this->createCalendar('The calendar of the tests');
        }

        return $this->calendar;
    }

    /**
     * Create a calendar.
     */
    private function createCalendar(string $name): Calendar
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $calendar = new Calendar();
        $calendar->setName($name);
        $entityManager->persist($calendar);
        $entityManager->flush();

        return $calendar;
    }
}
