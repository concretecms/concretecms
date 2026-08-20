<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that displays an alias of another block.
 *
 * @see \Concrete\Block\CoreScrapbookDisplay\Controller::getApiValueSchema()
 * @see \Concrete\Block\CoreScrapbookDisplay\Controller::serializeValueForApi()
 */
class CoreScrapbookDisplayApiValueTest extends BlockApiValueTestCase
{
    /**
     * The block that the tested block is an alias of.
     *
     * @var \Concrete\Core\Block\Block|null
     */
    private $originalBlock;

    public function setUp(): void
    {
        parent::setUp();
        $this->originalBlock = null;
    }

    public function testTheValueRefersToTheOriginalBlockInsteadOfBeingItsCifRepresentation(): void
    {
        $block = $this->addBlock();

        // the CIF representation of this block is the one of the block it's an alias of
        $blockNode = simplexml_load_string('<root />');
        $block->export($blockNode);
        static::assertSame('content', (string) $blockNode->block['type']);
        // ... but a JSON value can refer to a block by its ID
        static::assertSame(['bOriginalID' => (string) $this->getOriginalBlock()->getBlockID()], $this->getApiValue($block));
    }

    public function testTheAliasedBlockCanBeChanged(): void
    {
        $block = $this->addBlock();
        $anotherBlock = $this->createContentBlock('Another block');

        $this->updateBlock($block, ['bOriginalID' => (string) $anotherBlock->getBlockID()]);

        static::assertSame(
            ['bOriginalID' => (string) $anotherBlock->getBlockID()],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'core_scrapbook_display';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        return ['bOriginalID' => $this->getOriginalBlock()->getBlockID()];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        return ['bOriginalID' => (string) $this->getOriginalBlock()->getBlockID()];
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

    /**
     * Get the block that the tested block is an alias of.
     */
    private function getOriginalBlock(): Block
    {
        if ($this->originalBlock === null) {
            $this->originalBlock = $this->createContentBlock('The original block');
        }

        return $this->originalBlock;
    }

    /**
     * Create in another page a content block that can be aliased.
     */
    private function createContentBlock(string $content): Block
    {
        if (BlockType::getByHandle('content') === null) {
            BlockType::installBlockType('content');
        }
        $page = self::createPage('Page with ' . $content);
        $page->addBlock(BlockType::getByHandle('content'), 'Main', ['content' => $content]);

        return $this->getBlock($page);
    }
}
