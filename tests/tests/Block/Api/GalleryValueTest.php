<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Database\Connection\Connection;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the gallery block.
 *
 * @see \Concrete\Block\Gallery\Controller::getApiValueSchema()
 * @see \Concrete\Block\Gallery\Controller::serializeValueForApi()
 * @see \Concrete\Block\Gallery\Controller::getImportDataFromApiValue()
 */
class GalleryValueTest extends BlockApiValueTestCase
{
    public function testTheImagesAreReplacedWhenTheyAreSpecified(): void
    {
        $block = $this->addBlock();
        $entries = [$this->getApiEntry('Another caption', 'standard')];

        $this->updateBlock($block, ['entries' => $entries]);

        static::assertSame($entries, $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    public function testTheImagesAreDeletedWhenAnEmptyListIsSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['entries' => []]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    /**
     * @dataProvider providerFileReferences
     *
     * @param string $format the format of the reference to the file, with %1$s being its ID and %2$s its UUID
     */
    public function testTheFileReferencesAreResolved(string $format): void
    {
        $file = $this->getFile();
        $block = $this->addBlock();
        $reference = sprintf($format, $file->getFileID(), $file->getFileUUID());

        $this->updateBlock($block, ['entries' => [['fID' => $reference, 'displayChoices' => []]]]);

        $db = $this->app->make(Connection::class);
        static::assertSame(
            (string) $file->getFileID(),
            (string) $db->fetchOne('select fID from btGalleryEntries where bID = ?', [$block->getBlockID()])
        );
    }

    public static function providerFileReferences(): array
    {
        return [
            'a placeholder with the UUID' => ['{ccm:export:file::id=%2$s}'],
            'a placeholder with the ID' => ['{ccm:export:file::id=%1$s}'],
            'the local ID' => ['%1$s'],
        ];
    }

    public function testTheDisplayChoicesAreDescribedByTheSchema(): void
    {
        $block = $this->addBlock();

        $schema = $this->app->make(ApiValueSchemaFactory::class)->getSchema($block->getController());
        $displayChoices = $schema['properties']['entries']['items']['properties']['displayChoices'];

        static::assertFalse($displayChoices['additionalProperties']);
        static::assertSame(['caption', 'hover_caption', 'size'], array_keys($displayChoices['properties']));
        static::assertSame('string', $displayChoices['properties']['caption']['type']);
        static::assertArrayNotHasKey('enum', $displayChoices['properties']['caption']);
        static::assertSame(['', 'wide', 'standard'], $displayChoices['properties']['size']['enum']);
        static::assertSame('standard', $displayChoices['properties']['size']['default']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'gallery';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // the save() method wants the images as a JSON document
        $fieldJson = [];
        foreach ($this->getApiEntries() as $entry) {
            $displayChoices = [];
            foreach ($entry['displayChoices'] as $key => $value) {
                $displayChoices[$key] = ['value' => $value];
            }
            $fieldJson[] = ['id' => $this->getFile()->getFileID(), 'displayChoices' => $displayChoices];
        }

        return [
            'includeDownloadLink' => 1,
            'field_json' => json_encode($fieldJson),
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
            'includeDownloadLink' => '1',
            'entries' => $this->getApiEntries(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['includeDownloadLink' => '0'];
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
     * Get an image of the gallery, as it's exchanged via the API.
     *
     * @return array<string,mixed>
     */
    private function getApiEntry(string $caption, string $size): array
    {
        return [
            'fID' => '{ccm:export:file::id=' . $this->getFile()->getFileUUID() . '}',
            'displayChoices' => [
                'caption' => $caption,
                'hover_caption' => '',
                'size' => $size,
            ],
        ];
    }

    /**
     * Get the images used by the tests.
     *
     * @return array[]
     */
    private function getApiEntries(): array
    {
        return [$this->getApiEntry('A caption', 'wide')];
    }
}
