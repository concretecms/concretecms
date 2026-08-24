<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the RSS displayer block.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\RssDisplayer\Controller::getApiValueSchema()
 * @see \Concrete\Block\RssDisplayer\Controller::getImportDataFromApiValue()
 */
class RssDisplayerApiValueTest extends BlockApiValueTestCase
{
    public function testACustomDateFormatIsKeptAsItIs(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'dateFormat' => 'Y-m-d H.i.s',
        ]);

        static::assertSame(
            'Y-m-d H.i.s',
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['dateFormat']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'rss_displayer';
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
            'url' => 'https://www.concretecms.com/rss/blog',
            'title' => 'This is our feed title',
            'titleFormat' => 'h2',
            'itemsToDisplay' => 9,
            'showSummary' => 1,
            'launchInNewWindow' => 1,
            'standardDateFormat' => ':longDate:',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btRssDisplay table
        return [
            'title' => 'This is our feed title',
            'url' => 'https://www.concretecms.com/rss/blog',
            'dateFormat' => ':longDate:',
            'itemsToDisplay' => '9',
            'showSummary' => '1',
            'launchInNewWindow' => '1',
            'titleFormat' => 'h2',
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
