<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\TestHelpers\Page\PageTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class ImportDataFromValueTest extends PageTestCase
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

        static::assertSame(
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

        static::assertSame('<p>Some content with an & and a <em>tag</em></p>', $args['content']);
    }

    public function testNullValuesAreKeptDistinctFromEmptyStrings(): void
    {
        $page = self::createPage('Some page');
        $controller = $this->getContentBlockController();

        $args = $controller->getImportDataFromApiValue($page, [
            'content' => null,
            'displayOrder' => '',
        ]);

        static::assertArrayHasKey('content', $args);
        static::assertNull($args['content']);
        static::assertSame('', $args['displayOrder']);
    }

    public function testAValueMentioningOneSettingKeepsTheOtherOnes(): void
    {
        $page = self::createPage('Page with a block');
        if (BlockType::getByHandle('next_previous') === null) {
            BlockType::installBlockType('next_previous');
        }
        $page->addBlock(BlockType::getByHandle('next_previous'), 'Main', [
            'nextLabel' => 'Next one',
            'previousLabel' => 'Previous one',
            'parentLabel' => 'Up',
            'orderBy' => 'display_asc',
        ]);
        $blocks = $page->getBlocks('Main');
        $controller = $blocks[0]->getController();

        $args = $controller->getImportDataFromApiValue($page, ['nextLabel' => 'The one after this']);

        static::assertSame('The one after this', $args['nextLabel']);
        // the save() method of a block type is given every setting: the ones that the value doesn't
        // mention would be emptied
        static::assertSame('Previous one', $args['previousLabel']);
        static::assertSame('Up', $args['parentLabel']);
        static::assertSame('display_asc', $args['orderBy']);
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

        static::assertSame('Hello', $args['content']);
        static::assertSame('kept', $args['not a valid element name']);
        static::assertSame(['a' => 'b'], $args['nested']);
        static::assertTrue($args['flag']);
    }
}
