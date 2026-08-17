<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\TestHelpers\Page\PageTestCase;

class ImportDataFromApiValueTest extends PageTestCase
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

    /**
     * @return \Concrete\Core\Block\BlockController
     */
    private function getContentBlockController()
    {
        if (BlockType::getByHandle('content') === null) {
            BlockType::installBlockType('content');
        }
        // fetch it again: installBlockType() doesn't load the controller
        $blockType = BlockType::getByHandle('content');

        return $blockType->getController();
    }

    public function testPlaceholdersAreResolved(): void
    {
        $page = self::createPage('Target page');
        $cID = $page->getCollectionID();
        $controller = $this->getContentBlockController();

        $args = $controller->getImportDataFromApiValue($page, [
            'content' => '<a href="{ccm:export:page::id=' . $cID . '}">by ID</a> <a href="{ccm:export:page:/target-page}">by path</a>',
        ]);

        $this->assertSame(
            '<a href="{CCM:CID_' . $cID . '}">by ID</a> <a href="{CCM:CID_' . $cID . '}">by path</a>',
            $args['content']
        );
    }

    public function testValuesWithoutPlaceholdersAreLeftAlone(): void
    {
        $page = self::createPage('Some page');
        $controller = $this->getContentBlockController();

        $args = $controller->getImportDataFromApiValue($page, [
            'content' => '<p>Some content with an & and a <em>tag</em></p>',
        ]);

        $this->assertSame('<p>Some content with an & and a <em>tag</em></p>', $args['content']);
    }

    public function testNullValuesAreKeptDistinctFromEmptyStrings(): void
    {
        $page = self::createPage('Some page');
        $controller = $this->getContentBlockController();

        $args = $controller->getImportDataFromApiValue($page, [
            'content' => null,
            'displayOrder' => '',
        ]);

        $this->assertArrayHasKey('content', $args);
        $this->assertNull($args['content']);
        $this->assertSame('', $args['displayOrder']);
    }

    public function testValuesThatCantBeExpressedInXmlAreLeftUntouched(): void
    {
        $page = self::createPage('Some page');
        $controller = $this->getContentBlockController();

        $args = $controller->getImportDataFromApiValue($page, [
            'content' => 'Hello',
            'not a valid element name' => 'kept',
            'nested' => ['a' => 'b'],
            'flag' => true,
        ]);

        $this->assertSame('Hello', $args['content']);
        $this->assertSame('kept', $args['not a valid element name']);
        $this->assertSame(['a' => 'b'], $args['nested']);
        $this->assertTrue($args['flag']);
    }
}
