<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\File\Set\Set as FileSet;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the document library block.
 *
 * @see \Concrete\Block\DocumentLibrary\Controller::getApiValueSchema()
 * @see \Concrete\Block\DocumentLibrary\Controller::serializeValueForApi()
 * @see \Concrete\Block\DocumentLibrary\Controller::getImportDataFromApiValue()
 */
class DocumentLibraryValueTest extends BlockApiValueTestCase
{
    /**
     * The file sets created by the tests, by their name.
     *
     * @var \Concrete\Core\File\Set\Set[]
     */
    private $fileSets = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'FileSets',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->fileSets = [];
    }

    public function testTheFileSetsAreExchangedByTheirID(): void
    {
        $block = $this->addBlock();

        static::assertSame(
            [$this->getFileSetID('Documents')],
            $this->getApiValue($block)['setIds']
        );
    }

    public function testTheFileSetsCanBeCleared(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'setIds' => [],
        ]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['setIds']);
    }

    public function testThePropertiesAreExchangedAsTheyAreStored(): void
    {
        $block = $this->addBlock();
        $viewProperties = ['filename' => '5', 'size' => '1', 'date' => '-1'];

        $this->updateBlock($block, [
            'viewProperties' => $viewProperties,
        ]);

        static::assertSame(
            $viewProperties,
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['viewProperties']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'document_library';
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
            'folderID' => 0,
            'showFolders' => 1,
            'fsID' => [$this->getFileSetID('Documents')],
            'setMode' => 'all',
            'tags' => 'tag1',
            'onlyCurrentUser' => 1,
            'orderBy' => 'date',
            'displayOrderDesc' => 1,
            'displayLimit' => 25,
            'viewProperties' => ['thumbnail' => '5', 'filename' => '1'],
            'expandableProperties' => ['description'],
            'enableSearch' => 1,
            'searchProperties' => ['keywords'],
            'maxThumbWidth' => 120,
            'maxThumbHeight' => 160,
            'heightMode' => 'fixed',
            'fixedHeightSize' => 400,
            'downloadFileMethod' => 'browser',
            'tableName' => 'Our documents',
            'tableDescription' => 'The documents we share',
            'tableStriped' => 1,
            'rowBackgroundColorAlternate' => 'rgb(255, 217, 102)',
            'headerBackgroundColor' => 'rgb(255, 0, 0)',
            'headerBackgroundColorActiveSort' => 'rgb(4, 244, 50)',
            'headerTextColor' => 'rgb(41, 134, 204)',
            'allowFileUploading' => 1,
            'allowInPageFileManagement' => 1,
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
            'setIds' => [$this->getFileSetID('Documents')],
            'folderID' => '0',
            'setMode' => 'all',
            'onlyCurrentUser' => '1',
            'tags' => 'tag1',
            'viewProperties' => ['thumbnail' => '5', 'filename' => '1'],
            'expandableProperties' => ['description'],
            'searchProperties' => ['keywords'],
            'orderBy' => 'date',
            'displayLimit' => '25',
            'displayOrderDesc' => '1',
            'addFilesToSetID' => '0',
            'maxThumbWidth' => '120',
            'maxThumbHeight' => '160',
            'enableSearch' => '1',
            'heightMode' => 'fixed',
            'downloadFileMethod' => 'browser',
            'fixedHeightSize' => '400',
            'headerBackgroundColor' => 'rgb(255, 0, 0)',
            'headerBackgroundColorActiveSort' => 'rgb(4, 244, 50)',
            'headerTextColor' => 'rgb(41, 134, 204)',
            'allowFileUploading' => '1',
            'allowInPageFileManagement' => '1',
            'tableName' => 'Our documents',
            'tableDescription' => 'The documents we share',
            'tableStriped' => '1',
            'rowBackgroundColorAlternate' => 'rgb(255, 217, 102)',
            'hideFolders' => '0',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['tableName' => 'Other documents'];
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
     * Get the ID of a file set (it's created the first time it's asked for).
     */
    private function getFileSetID(string $name): int
    {
        if (!isset($this->fileSets[$name])) {
            // the tests of a class share the tables: the file set may have been created by another one
            $fileSet = FileSet::getByName($name);
            $this->fileSets[$name] = $fileSet ?: FileSet::add($name);
        }

        return (int) $this->fileSets[$name]->getFileSetID();
    }
}
