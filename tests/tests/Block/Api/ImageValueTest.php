<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\TypeFileSet as ThumbnailTypeFileSet;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the image block.
 *
 * @see \Concrete\Block\Image\Controller::getApiValueSchema()
 * @see \Concrete\Block\Image\Controller::serializeValueForApi()
 * @see \Concrete\Block\Image\Controller::getImportDataFromApiValue()
 */
class ImageValueTest extends BlockApiValueTestCase
{
    /**
     * The thumbnail type displayed at the breakpoints of the theme.
     *
     * @var \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type|null
     */
    private $thumbnailType;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            // the thumbnails of the files are generated for the file sets a thumbnail type is limited to
            ThumbnailTypeFileSet::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->thumbnailType = null;
    }

    public function testTheThumbnailsAreExchangedByTheirID(): void
    {
        $block = $this->addBlock();

        static::assertSame(
            ['xl' => $this->getThumbnailTypeID(), 'lg' => $this->getThumbnailTypeID()],
            $this->getApiValue($block)['thumbnails']
        );
    }

    public function testTheThumbnailsAreReplacedWhenTheyAreSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'thumbnails' => ['md' => $this->getThumbnailTypeID()],
        ]);

        static::assertSame(
            ['md' => $this->getThumbnailTypeID()],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['thumbnails']
        );
    }

    public function testTheThumbnailsAreDeletedWhenAnEmptyListIsSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'thumbnails' => [],
        ]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['thumbnails']);
    }

    /**
     * The flags of a block are stored as 0 and 1, and read back as strings: whoever writes them can use the
     * booleans that JSON has.
     */
    public function testTheFlagsCanBeWrittenAsBooleans(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'lazyLoad' => false,
            'openLinkInNewWindow' => true,
        ]);

        $value = $this->getApiValue($this->getBlock($block->getBlockCollectionObject()));
        static::assertSame('0', $value['lazyLoad']);
        static::assertSame('1', $value['openLinkInNewWindow']);
    }

    /**
     * The save() method of a block type is given the checkboxes of its form, which are sent only when they
     * are checked: a flag whose value is turned off must not be read as a checked one.
     */
    public function testAFlagTurnedOffIsNotReadAsATurnedOnOne(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'sizingOption' => 'constrain_size',
            'maxWidth' => 400,
            'maxHeight' => 300,
            'cropImage' => false,
        ]);

        static::assertSame('0', $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['cropImage']);
    }

    /**
     * The validation of a block type reads the checkboxes of its form too: a flag whose value is turned off
     * must not be complained about.
     */
    public function testAFlagTurnedOffIsNotValidatedAsATurnedOnOne(): void
    {
        $block = $this->addBlock();

        $args = $this->getImportData($block, [
            'sizingOption' => 'constrain_size',
            'maxWidth' => 0,
            'maxHeight' => 0,
            'cropImage' => false,
        ]);

        // cropping an image requires a maximum width and a maximum height, but nothing is being cropped
        static::assertSame([], $block->getController()->validate($args)->getList());
    }

    /**
     * The destination picker of the form of a block asks for the link in its own way: a value received via
     * the API carries the columns it fills, so there's nothing to be picked.
     */
    public function testTheValidationDoesntAskForTheFieldsOfTheForm(): void
    {
        $block = $this->addBlock();

        $args = $this->getImportData($block, ['internalLinkCID' => 0, 'fileLinkID' => 0, 'externalLink' => '']);

        static::assertSame([], $block->getController()->validate($args)->getList());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'image';
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
            'fID' => $this->getFile()->getFileID(),
            'fOnstateID' => 0,
            'altText' => 'Image alternative text',
            'title' => 'Image title',
            'lazyLoad' => 1,
            'sizingOption' => 'thumbnails_configurable',
            'selectedThumbnailTypes' => [
                'xl' => $this->getThumbnailTypeID(),
                'lg' => $this->getThumbnailTypeID(),
            ],
            'imageLink__which' => 'external_url',
            'imageLink_external_url' => 'https://www.example.com',
            'openLinkInNewWindow' => 1,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btContentImage table, with the thumbnails at the end
        return [
            'fID' => $this->getFile()->getFileUUID(),
            'fOnstateID' => '0',
            'cropImage' => '0',
            'maxWidth' => '0',
            'maxHeight' => '0',
            'externalLink' => 'https://www.example.com',
            'internalLinkCID' => '0',
            'fileLinkID' => '0',
            'openLinkInNewWindow' => '1',
            'altText' => 'Image alternative text',
            'title' => 'Image title',
            'lazyLoad' => '1',
            'sizingOption' => 'thumbnails_configurable',
            'thumbnails' => [
                'xl' => $this->getThumbnailTypeID(),
                'lg' => $this->getThumbnailTypeID(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['altText' => 'Another description'];
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
     * Get the ID of the thumbnail type used by the tests (it's created the first time it's asked for).
     */
    private function getThumbnailTypeID(): int
    {
        if ($this->thumbnailType === null) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            // the tests of a class share the tables: the thumbnail type may have been created by another one
            $thumbnailType = $entityManager->getRepository(ThumbnailType::class)->findOneBy(['ftTypeHandle' => 'wide']);
            if ($thumbnailType === null) {
                $thumbnailType = new ThumbnailType();
                $thumbnailType->setHandle('wide');
                $thumbnailType->setName('Wide');
                $thumbnailType->setWidth(1000);
                $thumbnailType->setHeight(500);
                $thumbnailType->setSizingMode(ThumbnailType::RESIZE_EXACT);
                $entityManager->persist($thumbnailType);
                $entityManager->flush();
            }
            $this->thumbnailType = $thumbnailType;
        }

        return (int) $this->thumbnailType->getID();
    }
}
