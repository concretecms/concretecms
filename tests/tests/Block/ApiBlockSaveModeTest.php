<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\Command\AddBlockToPageCommand;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\TestHelpers\Page\PageTestCase;

/**
 * The values received by the API are in the CIF format, so they must be saved the very same way an
 * import from a CIF file does: some block controllers behave differently in the two cases.
 *
 * @see \Concrete\Core\Api\Controller\Areas
 */
class ApiBlockSaveModeTest extends PageTestCase
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
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType
     */
    private function getImageBlockType()
    {
        if (BlockType::getByHandle('image') === null) {
            BlockType::installBlockType('image');
        }
        // fetch it again: installBlockType() doesn't load the controller
        return BlockType::getByHandle('image');
    }

    /**
     * @return \Concrete\Core\Block\Block
     */
    private function addImageBlock(\Concrete\Core\Page\Page $page, array $value, string $saveMode)
    {
        $blockType = $this->getImageBlockType();
        $command = new AddBlockToPageCommand();
        $command->setPage($page);
        $command->setArea(Area::getOrCreate($page, 'Main'));
        $command->setBlockType($blockType);
        $command->setData($blockType->getController()->getImportDataFromApiValue($page, $value));
        $command->setSaveMode($saveMode);

        return app()->executeCommand($command);
    }

    public function testInternalLinksSurviveTheApiSaveMode(): void
    {
        $page = self::createPage('Linked page');
        $target = self::createPage('Target page');
        $value = [
            'fID' => 0,
            'internalLinkCID' => (string) $target->getCollectionID(),
            'altText' => 'Lorem ipsum',
        ];

        $block = $this->addImageBlock($page, $value, SaveMode::SAVE_MODE_IMPORT);
        $controller = $block->getController();

        $this->assertSame((int) $target->getCollectionID(), (int) $controller->internalLinkCID);
        $this->assertSame('Lorem ipsum', $controller->altText);
    }

    /**
     * Documents why SAVE_MODE_IMPORT is needed: with the default save mode, the image block controller
     * decodes the "imageLink" destination picker, which isn't part of the CIF representation, and
     * therefore resets the link.
     */
    public function testInternalLinksAreLostWithTheDefaultSaveMode(): void
    {
        $page = self::createPage('Linked page 2');
        $target = self::createPage('Target page 2');
        $value = [
            'fID' => 0,
            'internalLinkCID' => (string) $target->getCollectionID(),
        ];

        $block = $this->addImageBlock($page, $value, SaveMode::SAVE_MODE_REQUEST);

        $this->assertSame(0, (int) $block->getController()->internalLinkCID);
    }
}
