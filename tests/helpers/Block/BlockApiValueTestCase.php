<?php

declare(strict_types=1);

namespace Concrete\TestHelpers\Block;

use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Api\Fractal\Transformer\BaseBlockTransformer;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentExporterOptions;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type as StorageLocationType;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Page\PageTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Base test case for the value that a block type exchanges via the API.
 *
 * It works with any block type: the ones letting the API derive their value from their database table as
 * well as the ones building it themselves.
 */
abstract class BlockApiValueTestCase extends PageTestCase
{
    /**
     * The file that the tests can refer to, in order to check how the references are exchanged.
     *
     * @var \Concrete\Core\Entity\File\File|null
     */
    private $file;

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
            'CollectionVersionBlocksOutputCache',
            'TreeTypes',
            'TreeNodeTypes',
            'TreeNodes',
            'TreeFileFolderNodes',
            'TreeFileNodes',
            'TreeNodePermissionAssignments',
            'Trees',
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
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\File\Version',
            'Concrete\Core\Entity\File\StorageLocation\StorageLocation',
            'Concrete\Core\Entity\File\StorageLocation\Type\Type',
            'Concrete\Core\Entity\File\Image\Thumbnail\Type\Type',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Statistics\UsageTracker\FileUsageRecord',
            'Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->file = null;
        $this->deleteStorageDirectory();
        // the API exports the references as IDs: let's do the same, since we aren't serving an API request
        ContentExporter::setOptions(new ContentExporterOptions(Request::create('/ccm/api/1.0/pages')));
    }

    public function tearDown(): void
    {
        ContentExporter::setOptions(new ContentExporterOptions(Request::create('/')));
        $this->deleteStorageDirectory();
        parent::tearDown();
    }

    public function testTheValueIsTheExpectedOne(): void
    {
        static::assertSame($this->getExpectedApiValue(), $this->getApiValue($this->addBlock()));
    }

    public function testTheValueSurvivesARoundTrip(): void
    {
        $block = $this->addBlock();
        $value = $this->getApiValue($block);

        $this->updateBlock($block, $value);

        static::assertSame($value, $this->getApiValue($this->getBlock($block->getBlockCollectionObject())));
    }

    public function testAPartialUpdateKeepsTheRestOfTheValue(): void
    {
        $partialValue = $this->getPartialApiValue();
        if ($partialValue === []) {
            static::markTestSkipped('No partial value to be tested');
        }
        $block = $this->addBlock();

        $this->updateBlock($block, $partialValue);

        static::assertSame(
            array_merge($this->getExpectedApiValue(), $partialValue),
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))
        );
    }

    public function testTheValueIsDescribedByTheSchema(): void
    {
        $block = $this->addBlock();

        $schema = $this->app->make(ApiValueSchemaFactory::class)->getSchema($block->getController());

        static::assertSame('object', $schema['type']);
        foreach (array_keys($this->getExpectedApiValue()) as $key) {
            static::assertArrayHasKey($key, $schema['properties'], "The schema doesn't describe the '{$key}' key");
        }
        // the schemas that aren't written by the controller are just an approximation of what it accepts
        static::assertSame($this->hasCustomApiValue(), !isset($schema['x-concrete-derived']));
    }

    /**
     * Get a file that the tests can refer to (it's created the first time it's asked for).
     *
     * @return \Concrete\Core\Entity\File\File
     */
    protected function getFile()
    {
        if ($this->file === null) {
            $this->app->make(Filesystem::class)->create();
            $storageLocationType = StorageLocationType::add('local', t('Local Storage'));
            $configuration = $storageLocationType->getConfigurationObject();
            $configuration->setRootPath($this->getStorageDirectory());
            $configuration->setWebRootRelativePath('/application/files');
            StorageLocation::add($configuration, 'Default', true);
            $version = $this->app->make(FileImporter::class)->importLocalFile(DIR_TESTS . '/assets/File/StorageLocation/tiny.png', 'tiny.png');
            $this->file = $version->getFile();
        }

        return $this->file;
    }

    /**
     * Get the directory where the files created by the tests are stored.
     */
    protected function getStorageDirectory(): string
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/files';
    }

    /**
     * Get the handle of the block type to be tested.
     */
    abstract protected function getBlockTypeHandle(): string;

    /**
     * Get the data to be passed to the save() method of the controller in order to create the block used by
     * the tests.
     *
     * @return array<string,mixed>
     */
    abstract protected function getSaveData(): array;

    /**
     * Get the value that the API is expected to expose for the block created with getSaveData().
     *
     * @return array<string,mixed>
     */
    abstract protected function getExpectedApiValue(): array;

    /**
     * Get a value that changes a part of the block, leaving the rest of it untouched.
     *
     * @return array<string,mixed> an empty array if the block has nothing that can be updated partially
     */
    protected function getPartialApiValue(): array
    {
        return [];
    }

    /**
     * Does the controller build the value (and its schema) itself, instead of letting the API derive them
     * from its database table?
     */
    protected function hasCustomApiValue(): bool
    {
        return false;
    }

    /**
     * Add to a page a block of the type to be tested.
     *
     * @param array<string,mixed>|null $saveData the data to be passed to the save() method (NULL: the one
     *                                           returned by getSaveData())
     */
    protected function addBlock(?Page $page = null, ?array $saveData = null): Block
    {
        if (BlockType::getByHandle($this->getBlockTypeHandle()) === null) {
            BlockType::installBlockType($this->getBlockTypeHandle());
        }
        if ($page === null) {
            $page = self::createPage('Page with a ' . $this->getBlockTypeHandle());
        }
        $page->addBlock(
            BlockType::getByHandle($this->getBlockTypeHandle()),
            'Main',
            $saveData === null ? $this->getSaveData() : $saveData
        );

        return $this->getBlock($page);
    }

    /**
     * Update a block with a value received via the API.
     *
     * @param array<string,mixed> $value
     */
    protected function updateBlock(Block $block, array $value): void
    {
        $page = $block->getBlockCollectionObject();
        $block->update($block->getController()->getImportDataFromApiValue($page, $value));
    }

    /**
     * Get the block of a page.
     */
    protected function getBlock(Page $page): Block
    {
        $blocks = $page->getBlocks('Main');

        return $blocks[0];
    }

    /**
     * Get the value of a block as it's exposed by the API.
     *
     * @return array<string,mixed>
     */
    protected function getApiValue(Block $block): array
    {
        $transformed = (new BaseBlockTransformer())->transform($block);

        return $transformed['value'];
    }

    /**
     * Delete the directory holding the files created by the tests.
     */
    private function deleteStorageDirectory(): void
    {
        if (!is_dir($this->getStorageDirectory())) {
            return;
        }
        $contents = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getStorageDirectory(), \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($contents as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }
        rmdir($this->getStorageDirectory());
    }
}
