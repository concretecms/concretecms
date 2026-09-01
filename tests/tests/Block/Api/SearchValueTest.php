<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the search block.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\Search\Controller::getApiValueSchema()
 * @see \Concrete\Block\Search\Controller::getImportDataFromApiValue()
 */
class SearchValueTest extends BlockApiValueTestCase
{
    public function testTheResultsCanBeDisplayedByAnotherPage(): void
    {
        $block = $this->addBlock();
        $page = $block->getBlockCollectionObject();

        $this->updateBlock($block, [
            'postTo_cID' => (string) $page->getCollectionID(),
            'resultsURL' => '',
        ]);

        static::assertSame(
            (string) $page->getCollectionID(),
            $this->getApiValue($this->getBlock($page))['postTo_cID']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'search';
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
            'title' => 'This is the title',
            'buttonText' => 'This is the button test',
            'baseSearchPath' => 'ALL',
            'resultsPageKind' => 'URL',
            'resultsURL' => '/another/url',
            'allowUserOptions' => 'ALLOW',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btSearch table
        return [
            'title' => 'This is the title',
            'buttonText' => 'This is the button test',
            'baseSearchPath' => '',
            'search_all' => '1',
            'allow_user_options' => '1',
            'postTo_cID' => null,
            'resultsURL' => '/another/url',
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
