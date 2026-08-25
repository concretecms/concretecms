<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the topic list block.
 *
 * The topic tree is exchanged by its ID: that's covered by the CIF test cases of the block, which have one
 * (a CIF file has to refer to it by its name).
 *
 * @see \Concrete\Block\TopicList\Controller::getApiValueSchema()
 * @see \Concrete\Block\TopicList\Controller::serializeValueForApi()
 * @see \Concrete\Block\TopicList\Controller::getImportDataFromApiValue()
 */
class TopicListValueTest extends BlockApiValueTestCase
{
    public function testThePageTheTopicsLinkToIsExchangedAsAReference(): void
    {
        $block = $this->addBlock();
        $page = $block->getBlockCollectionObject();

        $this->updateBlock($block, [
            'cParentID' => (string) $page->getCollectionID(),
        ]);

        static::assertSame(
            (string) $page->getCollectionID(),
            $this->getApiValue($this->getBlock($page))['cParentID']
        );
    }

    public function testATopicTreeThatDoesntExistIsDiscarded(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'topicTreeID' => 0x7FFFFFFF,
        ]);

        static::assertSame(0, $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['topicTreeID']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'topic_list';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends when the topics are the ones of the page
        return [
            'mode' => 'P',
            'topicTreeID' => 0,
            'topicAttributeKeyHandle' => 'test_topic',
            'title' => 'This is the Title!',
            'titleFormat' => 'h1',
            'externalTarget' => 0,
            'cParentID' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        return [
            'mode' => 'P',
            'topicTreeID' => 0,
            'topicAttributeKeyHandle' => 'test_topic',
            'cParentID' => '0',
            'title' => 'This is the Title!',
            'titleFormat' => 'h1',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['title' => 'Another title'];
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
