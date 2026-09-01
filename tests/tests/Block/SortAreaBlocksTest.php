<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\Command\AddBlockToPageCommand;
use Concrete\Core\Block\Command\SortAreaBlocksCommand;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Page\PageTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The blocks to be sorted must be all and only the blocks currently in the area.
 *
 * @see \Concrete\Core\Api\Controller\Areas::sortBlocks()
 */
class SortAreaBlocksTest extends PageTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'Blocks',
            'BlockTypeSets',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            BlockTypeEntity::class,
        ]);
    }

    public function testBlocksAreSorted(): void
    {
        $page = self::createPage('Sortable blocks');
        $blockIDs = $this->addBlocks($page, 3);

        $this->sortBlocks($page, array_reverse($blockIDs));

        static::assertSame(array_reverse($blockIDs), $this->getAreaBlockIDs($page));
    }

    public static function providerInvalidBlockIDs(): array
    {
        return [
            'a missing block' => [[0, 1]],
            'an unknown block' => [[0, 1, 2, 12345]],
            'a duplicated block' => [[0, 1, 2, 2]],
            'no blocks at all' => [[]],
        ];
    }

    /**
     * @dataProvider providerInvalidBlockIDs
     *
     * @param int[] $blockIndexes the indexes of the blocks of the area (12345 is an ID that's not in the area)
     */
    public function testTheBlocksMustBeAllAndOnlyTheBlocksOfTheArea(array $blockIndexes): void
    {
        $page = self::createPage('Wrongly sorted blocks ' . implode('-', $blockIndexes));
        $blockIDs = $this->addBlocks($page, 3);
        $sortedBlockIDs = [];
        foreach ($blockIndexes as $blockIndex) {
            $sortedBlockIDs[] = $blockIDs[$blockIndex] ?? $blockIndex;
        }

        try {
            $this->sortBlocks($page, $sortedBlockIDs);
            static::fail('Sorting the blocks should have failed');
        } catch (HandlerFailedException $x) {
            static::assertInstanceOf(\InvalidArgumentException::class, $x->getPrevious());
        }
        // the blocks should have been left untouched
        static::assertSame($blockIDs, $this->getAreaBlockIDs($page));
    }

    private function getImageBlockType(): BlockTypeEntity
    {
        if (BlockType::getByHandle('image') === null) {
            BlockType::installBlockType('image');
        }

        // fetch it again: installBlockType() doesn't load the controller
        return BlockType::getByHandle('image');
    }

    /**
     * @return int[] the IDs of the newly created blocks
     */
    private function addBlocks(Page $page, int $numBlocks, string $areaHandle = 'Main'): array
    {
        $blockType = $this->getImageBlockType();
        $blockIDs = [];
        for ($index = 1; $index <= $numBlocks; $index++) {
            $value = [
                'fID' => 0,
                'altText' => "Block #{$index}",
            ];
            $command = new AddBlockToPageCommand();
            // every command works on a new version of the page: let's start from the most recent one
            $command->setPage(Page::getByID($page->getCollectionID(), 'RECENT'));
            $command->setArea(Area::getOrCreate($page, $areaHandle));
            $command->setBlockType($blockType);
            $command->setData($blockType->getController()->getImportDataFromApiValue($page, $value));
            $command->setSaveMode(SaveMode::SAVE_MODE_IMPORT);
            $block = app()->executeCommand($command);
            static::assertInstanceOf(Block::class, $block);
            $blockIDs[] = $block->getBlockID();
        }

        return $blockIDs;
    }

    /**
     * @param int[] $blockIDs
     */
    private function sortBlocks(Page $page, array $blockIDs, string $areaHandle = 'Main'): void
    {
        $page = Page::getByID($page->getCollectionID(), 'RECENT');
        $command = new SortAreaBlocksCommand();
        $command->setPage($page);
        $command->setArea(Area::getOrCreate($page, $areaHandle));
        $command->setBlockIDs($blockIDs);
        app()->executeCommand($command);
    }

    /**
     * @return int[] the IDs of the blocks currently in the area, in their display order
     */
    private function getAreaBlockIDs(Page $page, string $areaHandle = 'Main'): array
    {
        $page = Page::getByID($page->getCollectionID(), 'RECENT');
        $rows = app(Connection::class)->fetchFirstColumn(
            'select bID from CollectionVersionBlocks where cID = ? and cvID = ? and arHandle = ? order by cbDisplayOrder asc',
            [$page->getCollectionID(), $page->getVersionID(), $areaHandle]
        );

        return array_map('intval', $rows);
    }
}
