<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Entity\Page\Feed as FeedEntity;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the page list block.
 *
 * @see \Concrete\Block\PageList\Controller::getApiValueSchema()
 * @see \Concrete\Block\PageList\Controller::serializeValueForApi()
 * @see \Concrete\Block\PageList\Controller::getImportDataFromApiValue()
 */
class PageListApiValueTest extends BlockApiValueTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            // the block publishes the listed pages in an RSS feed
            FeedEntity::class,
        ]);
    }

    public function testTheFeedIsCreatedByTheValueThatNamesIt(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'rssHandle' => 'the-listed-pages',
            'rssTitle' => 'The listed pages',
            'rssDescription' => 'What we published',
        ]);

        $feed = $this->app->make(EntityManagerInterface::class)
            ->getRepository(FeedEntity::class)
            ->findOneBy(['pfHandle' => 'the-listed-pages'])
        ;
        static::assertInstanceOf(FeedEntity::class, $feed);
        static::assertSame('The listed pages', $feed->getTitle());
        // ... and the block refers to it
        $value = $this->getApiValue($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame('{ccm:export:pagefeed::id=' . $feed->getID() . '}', $value['pfID']);
        static::assertSame('the-listed-pages', $value['rssHandle']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'page_list';
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
            'cParentID' => 0,
            'cThis' => 0,
            'includeAllDescendents' => 1,
            'displayAliases' => 1,
            'orderBy' => 'chrono_desc',
            'num' => 10,
            'paginate' => 1,
            'pageListTitle' => 'Title of Page List',
            'titleFormat' => 'h2',
            'includeName' => 1,
            'includeDescription' => 1,
            'truncateSummaries' => 1,
            'truncateChars' => 123,
            'includeDate' => 1,
            'displayThumbnail' => 1,
            'useButtonForLink' => 1,
            'buttonLinkText' => 'Link Text',
            'noResultsMessage' => 'Message to Display When No Pages Listed',
            'filterDateOption' => 'past',
            'filterDateDays' => 3,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btPageList table, with the ID of the topic at the end
        return [
            'num' => '10',
            'orderBy' => 'chrono_desc',
            'cParentID' => '0',
            'cThis' => '0',
            // the form of the block computes them out of cParentID: the whole site is listed
            'cThisParent' => '0',
            'useButtonForLink' => '1',
            'buttonLinkText' => 'Link Text',
            'pageListTitle' => 'Title of Page List',
            'filterByRelated' => '0',
            'filterByCustomTopic' => '0',
            'filterDateOption' => 'past',
            'filterDateDays' => '3',
            'filterDateStart' => null,
            'filterDateEnd' => null,
            'relatedTopicAttributeKeyHandle' => '',
            'customTopicAttributeKeyHandle' => '',
            'includeName' => '1',
            'includeDescription' => '1',
            'includeDate' => '1',
            'includeAllDescendents' => '1',
            'paginate' => '1',
            'excludeCanonicalPaging' => '0',
            'displayAliases' => '1',
            'displaySystemPages' => '0',
            'ignorePermissions' => '0',
            'enableExternalFiltering' => '0',
            'excludeCurrentPage' => '0',
            'ptID' => '0',
            'pfID' => '0',
            'truncateSummaries' => '1',
            'displayFeaturedOnly' => '0',
            'noResultsMessage' => 'Message to Display When No Pages Listed',
            'displayThumbnail' => '1',
            'truncateChars' => '123',
            'titleFormat' => 'h2',
            'customTopicTreeNodeID' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['pageListTitle' => 'Another title'];
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
