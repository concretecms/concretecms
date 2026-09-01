<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\Command\AddBlockToPageCommand;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Block\Exception\BlockNotFoundException;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Page\PageTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * By default new blocks are appended at the end of the area: the API lets clients place a new block
 * before an already existing one.
 *
 * @see \Concrete\Core\Api\Controller\Areas::addBlock()
 */
class AddBlockBeforeBlockTest extends PageTestCase
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

    public function testBlocksAreAppendedByDefault(): void
    {
        $page = self::createPage('Appended blocks');

        $first = $this->addBlock($page, 'First');
        $second = $this->addBlock($page, 'Second');
        $third = $this->addBlock($page, 'Third');

        static::assertSame(
            [$first->getBlockID(), $second->getBlockID(), $third->getBlockID()],
            $this->getAreaBlockIDs($page)
        );
    }

    public function testBlocksCanBePlacedBeforeAnotherBlock(): void
    {
        $page = self::createPage('Sorted blocks');

        $first = $this->addBlock($page, 'First');
        $last = $this->addBlock($page, 'Last');
        $middle = $this->addBlock($page, 'Middle', $last);
        $head = $this->addBlock($page, 'Head', $first);

        static::assertSame(
            [$head->getBlockID(), $first->getBlockID(), $middle->getBlockID(), $last->getBlockID()],
            $this->getAreaBlockIDs($page)
        );
    }

    public function testBlocksOfOtherAreasAreRejected(): void
    {
        $page = self::createPage('Other area blocks');

        $mainBlock = $this->addBlock($page, 'Main area block');
        $otherBlock = $this->addBlock($page, 'Other area block', null, 'Sidebar');

        try {
            $this->addBlock($page, 'New block', $otherBlock);
            static::fail('Adding a block before a block of another area should have failed');
        } catch (HandlerFailedException $x) {
            static::assertInstanceOf(BlockNotFoundException::class, $x->getPrevious());
        }
        // the new block should not have been created at all
        static::assertSame([$mainBlock->getBlockID()], $this->getAreaBlockIDs($page));
    }

    private function getImageBlockType(): BlockTypeEntity
    {
        if (BlockType::getByHandle('image') === null) {
            BlockType::installBlockType('image');
        }

        // fetch it again: installBlockType() doesn't load the controller
        return BlockType::getByHandle('image');
    }

    private function addBlock(Page $page, string $altText, ?Block $beforeBlock = null, string $areaHandle = 'Main'): Block
    {
        // every command works on a new version of the page: let's start from the most recent one
        $page = Page::getByID($page->getCollectionID(), 'RECENT');
        $blockType = $this->getImageBlockType();
        $value = [
            'fID' => 0,
            'altText' => $altText,
        ];
        $command = new AddBlockToPageCommand();
        $command->setPage($page);
        $command->setArea(Area::getOrCreate($page, $areaHandle));
        $command->setBlockType($blockType);
        $command->setData($blockType->getController()->getImportDataFromApiValue($page, $value));
        $command->setSaveMode(SaveMode::SAVE_MODE_IMPORT);
        $command->setBeforeBlock($beforeBlock);

        return app()->executeCommand($command);
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
