<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Form;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that displays an Express entry.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\ExpressEntryDetail\Controller::getApiValueSchema()
 */
class ExpressEntryDetailValueTest extends BlockApiValueTestCase
{
    /**
     * The ID of an Express entity (it doesn't have to exist: the block just refers to it).
     *
     * @var string
     */
    private const ENTITY_ID = '1cafebab-babe-cafe-babe-1cafebabe1ca';

    /**
     * The ID of a form of the Express entity.
     *
     * @var string
     */
    private const FORM_ID = '2cafebab-babe-cafe-babe-2cafebabe2ca';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            // the export() method of the block looks for the entity and for the form it refers to
            Entity::class,
            Form::class,
        ]);
    }

    public function testTheSchemaListsTheAvailableModes(): void
    {
        $block = $this->addBlock();

        $schema = $this->app->make(ApiValueSchemaFactory::class)->getSchema($block->getController());

        static::assertSame(['E', 'S', 'A'], $schema['properties']['entryMode']['enum']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'express_entry_detail';
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
            'entryMode' => 'S',
            'exEntityID' => self::ENTITY_ID,
            'exSpecificEntryID' => 12,
            'exEntryAttributeKeyHandle' => '',
            'exFormID' => self::FORM_ID,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btExpressEntryDetail table
        return [
            'exEntityID' => self::ENTITY_ID,
            'exSpecificEntryID' => '12',
            'exEntryAttributeKeyHandle' => '',
            'exFormID' => self::FORM_ID,
            'entryMode' => 'S',
        ];
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
}
