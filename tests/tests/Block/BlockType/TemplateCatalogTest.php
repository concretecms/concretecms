<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\BlockType;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\TemplateCatalog;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\File\Service\File;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The templates of the core and of the theme atomik that these tests look for are the ones that ship
 * with this repository: page_list has basic_list of its own, and atomik gives it two more.
 *
 * @see \Concrete\Core\Block\BlockType\TemplateCatalog
 */
class TemplateCatalogTest extends TestCase
{
    public function testTheTemplatesOfACoreBlockAreThereWhateverTheTheme(): void
    {
        $templates = $this->createCatalog()->getCustomTemplates($this->createBlockType('page_list'), null);

        static::assertSame(['basic_list.php'], $this->getFilenames($templates));
    }

    public function testThemeTemplatesAreHandedOverOnTheirOwn(): void
    {
        $templates = $this->createCatalog()->getThemeCustomTemplates($this->createBlockType('page_list'), $this->createTheme('atomik'));

        static::assertSame(['blog_entry_grid.php', 'resource_list.php'], $this->getFilenames($templates));
    }

    public function testABlockIsGivenItsOwnTemplatesAndTheOnesOfItsTheme(): void
    {
        $templates = $this->createCatalog()->getCustomTemplatesForBlock($this->createBlock('page_list', 'atomik'));

        static::assertSame(['basic_list.php', 'blog_entry_grid.php', 'resource_list.php'], $this->getFilenames($templates));
    }

    public function testABlockOnAPageWithNoThemeIsGivenOnlyTheTemplatesThatAreAlwaysThere(): void
    {
        $templates = $this->createCatalog()->getCustomTemplatesForBlock($this->createBlock('page_list', null));

        static::assertSame(['basic_list.php'], $this->getFilenames($templates));
    }

    public function testTheTemplatesAreNamedAfterTheirFile(): void
    {
        $templates = $this->createCatalog()->getThemeCustomTemplates($this->createBlockType('page_list'), $this->createTheme('atomik'));

        static::assertSame('Blog Entry Grid', $templates[0]->getTemplateFileName());
    }

    public function testABlockTakesTheTemplatesItIsGiven(): void
    {
        $catalog = $this->createCatalog();
        $block = $this->createBlock('page_list', 'atomik');

        static::assertTrue($catalog->isValidCustomTemplateForBlock($block, 'basic_list.php'));
        static::assertTrue($catalog->isValidCustomTemplateForBlock($block, 'resource_list.php'));
        // only the name a block type offers, since Block::updateBlockInformation() refuses a path
        static::assertFalse($catalog->isValidCustomTemplateForBlock($block, 'templates/resource_list.php'));
    }

    public function testABlockTakesNoOtherTemplate(): void
    {
        $catalog = $this->createCatalog();
        $block = $this->createBlock('page_list', 'atomik');

        static::assertFalse($catalog->isValidCustomTemplateForBlock($block, 'there_is_no_such_template.php'));
        // the one atomik gives to another block type
        static::assertFalse($catalog->isValidCustomTemplateForBlock($block, 'primary_border_top.php'));
        static::assertFalse($catalog->isValidCustomTemplateForBlock($block, ''));
        static::assertFalse($catalog->isValidCustomTemplateForBlock($block, '../../../view.php'));
    }

    public function testATypeTakesTheTemplatesOfTheThemeItIsAskedAbout(): void
    {
        $catalog = $this->createCatalog();
        $blockType = $this->createBlockType('page_list');

        static::assertTrue($catalog->isValidCustomTemplate($blockType, $this->createTheme('atomik'), 'resource_list.php'));
        static::assertTrue($catalog->isValidCustomTemplate($blockType, null, 'basic_list.php'));
        // the one atomik gives is not there for a page shown with no theme
        static::assertFalse($catalog->isValidCustomTemplate($blockType, null, 'resource_list.php'));
    }

    public function testABlockWhoseTypeIsGoneIsGivenNothing(): void
    {
        $catalog = $this->createCatalog();
        $block = $this->createMock(Block::class);
        $block->method('getBlockTypeObject')->willReturn(null);

        static::assertSame([], $catalog->getCustomTemplatesForBlock($block));
        static::assertFalse($catalog->isValidCustomTemplateForBlock($block, 'basic_list.php'));
    }

    public function testABlockTypeWithoutComposerTemplatesHasNone(): void
    {
        $templates = $this->createCatalog()->getComposerTemplates($this->createBlockType('page_list'));

        static::assertSame([], $templates);
    }

    /**
     * @param \Concrete\Core\Filesystem\TemplateFile[] $templates
     *
     * @return string[]
     */
    private function getFilenames(array $templates): array
    {
        $filenames = [];
        foreach ($templates as $template) {
            $filenames[] = $template->getTemplateFileFilename();
        }

        return $filenames;
    }

    private function createCatalog(): TemplateCatalog
    {
        return new class (app(File::class), $this->createMock(PackageService::class)) extends TemplateCatalog {
            /**
             * {@inheritdoc}
             *
             * @see \Concrete\Core\Block\BlockType\TemplateCatalog::getPackageDirectories()
             */
            protected function getPackageDirectories(): array
            {
                // no package is installed as far as these tests go, and none is to be asked for
                return [];
            }
        };
    }

    private function createBlockType(string $handle): BlockTypeEntity
    {
        $blockType = new BlockTypeEntity();
        $blockType->setBlockTypeHandle($handle);

        return $blockType;
    }

    private function createTheme(string $handle): Theme
    {
        $theme = $this->createMock(Theme::class);
        $theme->method('getThemeHandle')->willReturn($handle);
        $theme->method('getPackageHandle')->willReturn(null);

        return $theme;
    }

    /**
     * @param string|null $themeHandle the theme the page of the block is shown with, NULL for a page with none
     */
    private function createBlock(string $blockTypeHandle, ?string $themeHandle): Block
    {
        $page = $this->createMock(Page::class);
        $page->method('getCollectionThemeObject')->willReturn($themeHandle === null ? null : $this->createTheme($themeHandle));
        $block = $this->createMock(Block::class);
        $block->method('getBlockTypeObject')->willReturn($this->createBlockType($blockTypeHandle));
        $block->method('getBlockCollectionObject')->willReturn($page);

        return $block;
    }
}
