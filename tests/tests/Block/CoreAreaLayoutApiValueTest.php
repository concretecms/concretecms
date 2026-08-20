<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Block\CoreAreaLayout\Controller as CoreAreaLayoutController;
use Concrete\Core\Area\Area;
use Concrete\Core\Area\Layout\ThemeGridColumn;
use Concrete\Core\Area\SubArea;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that lays out the areas of a page.
 *
 * @see \Concrete\Block\CoreAreaLayout\Controller::getApiValueSchema()
 * @see \Concrete\Block\CoreAreaLayout\Controller::serializeValueForApi()
 * @see \Concrete\Block\CoreAreaLayout\Controller::getImportDataFromApiValue()
 */
class CoreAreaLayoutApiValueTest extends BlockApiValueTestCase
{
    /**
     * The block created by the tests.
     *
     * @var \Concrete\Core\Block\Block|null
     */
    private $block;

    public function setUp(): void
    {
        parent::setUp();
        $this->block = null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'AreaLayouts',
            'AreaLayoutColumns',
            'AreaLayoutPresets',
            'AreaLayoutThemeGridColumns',
            'AreaPermissionAssignments',
        ]);
    }

    public function testTheAreasOfTheColumnsAreCreatedWithTheBlock(): void
    {
        $block = $this->addBlock();
        $page = $block->getBlockCollectionObject();

        foreach ($this->getApiValue($block)['columns'] as $column) {
            // the area is there even if the page has never been displayed
            $area = Area::get($page, $column['area']);
            static::assertInstanceOf(SubArea::class, $area, "The '{$column['area']}' area hasn't been created");
            static::assertSame((int) $block->getBlockAreaObject()->getAreaID(), (int) $area->getAreaParentID());
        }
    }

    public function testTheColumnsCanBeResized(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['columns' => [['span' => '4', 'offset' => '2'], ['span' => '6', 'offset' => '0']]]);

        static::assertSame(
            $this->getExpectedColumns([['span' => '4', 'offset' => '2'], ['span' => '6', 'offset' => '0']]),
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['columns']
        );
    }

    public function testTheColumnsThatArentSpecifiedAreKept(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['columns' => [['span' => '4']]]);

        static::assertSame(
            $this->getExpectedColumns([['span' => '4', 'offset' => '0'], ['span' => '5', 'offset' => '1']]),
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['columns']
        );
    }

    public function testTheBlocksPlacedInTheColumnsSurvive(): void
    {
        $block = $this->addBlock();
        $innerBlock = $this->addBlockToColumn($block, 0, 'A block in the first column');

        $this->updateBlock($block, ['columns' => [['span' => '4'], ['span' => '8']]]);

        $page = $block->getBlockCollectionObject();
        $page->loadVersionObject('RECENT');
        $blocks = $page->getBlocks($innerBlock->getAreaHandle());
        static::assertCount(1, $blocks);
        static::assertSame('A block in the first column', $blocks[0]->getController()->content);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'core_area_layout';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends when a theme grid layout is created
        return [
            'gridType' => 'TG',
            'arLayoutMaxColumns' => 12,
            'themeGridColumns' => 2,
            'span' => [6, 5],
            'offset' => [0, 1],
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
            'type' => 'theme-grid',
            'maxColumns' => '12',
            'columns' => $this->getExpectedColumns([
                ['span' => '6', 'offset' => '0'],
                ['span' => '5', 'offset' => '1'],
            ]),
        ];
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
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::addBlock()
     */
    protected function addBlock(?Page $page = null, ?array $saveData = null): Block
    {
        return $this->block = parent::addBlock($page, $saveData);
    }

    /**
     * Add to the expected columns the handle of the area every one of them creates.
     *
     * @param array[] $columns
     *
     * @return array[]
     */
    private function getExpectedColumns(array $columns): array
    {
        static::assertInstanceOf(Block::class, $this->block);
        $controller = $this->block->getController();
        static::assertInstanceOf(CoreAreaLayoutController::class, $controller);
        $layoutArea = $this->block->getBlockAreaObject();
        foreach ($controller->getAreaLayoutObject()->getAreaLayoutColumns() as $index => $column) {
            $columns[$index]['area'] = $layoutArea->getAreaHandle() . SubArea::AREA_SUB_DELIMITER . $column->getAreaLayoutColumnDisplayID();
        }

        return $columns;
    }

    /**
     * Add a content block to one of the areas created by a layout block.
     */
    private function addBlockToColumn(Block $layoutBlock, int $columnIndex, string $content): Block
    {
        if (BlockType::getByHandle('content') === null) {
            BlockType::installBlockType('content');
        }
        $controller = $layoutBlock->getController();
        static::assertInstanceOf(CoreAreaLayoutController::class, $controller);
        $layoutArea = $layoutBlock->getBlockAreaObject();
        $page = $layoutBlock->getBlockCollectionObject();
        $columns = $controller->getAreaLayoutObject()->getAreaLayoutColumns();
        $column = $columns[$columnIndex];
        static::assertInstanceOf(ThemeGridColumn::class, $column);
        $subArea = new SubArea((string) $column->getAreaLayoutColumnDisplayID(), $layoutArea->getAreaHandle(), $layoutArea->getAreaID());
        $subArea->load($page);
        $page->addBlock(BlockType::getByHandle('content'), $subArea, ['content' => $content]);
        $blocks = $page->getBlocks($subArea->getAreaHandle());

        return $blocks[0];
    }
}
