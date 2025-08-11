<?php

namespace Concrete\Tests\Page;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\TestHelpers\Page\PageTestCase;
use Concrete\Core\Page\Search\IndexedSearch;

class ClonerTest extends PageTestCase
{
    public function testBasicCreatePage()
    {
        $indexer = new IndexedSearch();
        $bt = BlockType::getByHandle('content');
        if ($bt === null) {
            $bt = BlockType::installBlockType('content');
        }
        if (BlockType::getByHandle('core_scrapbook_display') === null) {
            BlockType::installBlockType('core_scrapbook_display');
        }
        $home = Page::getByID(Page::getHomePageID());
        $pageType = PageType::getByID(1);

        $page1 = $home->add($pageType, ['cName' => 'Cloner Test - Page 1'])->getVersionToModify();
        $page1->addBlock($bt, (new Area(''))->create($page1, 'Main'), ['content' => 'Content #1']);
        $this->assertSame('Content #1', trim($indexer->getBodyContentFromPage($page1)));

        $page2 = $page1->duplicate()->getVersionToModify();
        $this->assertSame('Content #1', trim($indexer->getBodyContentFromPage($page2)));

        $blocks = $page2->getBlocks('Main');
        $this->assertCount(1, $blocks);
        $block = $blocks[0];
        $this->assertTrue($block->isAlias());
        $newBlock = $block->duplicate($page2);
        $block->deleteBlock();

        $newBlock->update(['content' => 'Content #2']);
        $this->assertSame('Content #2', trim($indexer->getBodyContentFromPage($page2)));
        $this->assertSame('Content #1', trim($indexer->getBodyContentFromPage($page1)));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'Blocks',
            'CollectionVersionBlocksOutputCache',
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
}
