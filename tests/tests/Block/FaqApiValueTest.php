<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Database\Connection\Connection;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the FAQ block.
 *
 * @see \Concrete\Block\Faq\Controller::getApiValueSchema()
 * @see \Concrete\Block\Faq\Controller::serializeValueForApi()
 * @see \Concrete\Block\Faq\Controller::getImportDataFromApiValue()
 */
class FaqApiValueTest extends BlockApiValueTestCase
{
    public function testTheItemsAreReplacedWhenTheyAreSpecified(): void
    {
        $block = $this->addBlock();
        $entries = [$this->getApiEntry('Only one', '<p>Just one item</p>')];

        $this->updateBlock($block, ['entries' => $entries]);

        static::assertSame($entries, $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    public function testTheItemsAreDeletedWhenAnEmptyListIsSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['entries' => []]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    public function testThePlaceholdersOfTheItemsAreResolved(): void
    {
        $block = $this->addBlock();
        $page = $block->getBlockCollectionObject();
        $cID = $page->getCollectionID();
        $description = '<a href="{ccm:export:page::id=' . $cID . '}">by ID</a> <a href="{ccm:export:page:' . $page->getCollectionPath() . '}">by path</a>';

        $this->updateBlock($block, ['entries' => [$this->getApiEntry('Links', $description)]]);

        $db = $this->app->make(Connection::class);
        static::assertSame(
            '<a href="{CCM:CID_' . $cID . '}">by ID</a> <a href="{CCM:CID_' . $cID . '}">by path</a>',
            $db->fetchOne('select description from btFaqEntries where bID = ?', [$block->getBlockID()])
        );
    }

    public function testTheItemsAreExportedAsPlaceholders(): void
    {
        $page = self::createPage('Page with a link');
        $cID = $page->getCollectionID();
        $entry = $this->getApiEntry('Links', '<a href="{CCM:CID_' . $cID . '}">a link</a>');
        $block = $this->addBlock($page, $this->getSaveDataForEntries([$entry]));

        static::assertSame(
            [$this->getApiEntry('Links', '<a href="{ccm:export:page::id=' . $cID . '}">a link</a>')],
            $this->getApiValue($block)['entries']
        );
    }

    public function testTheFileReferencesOfTheItemsAreExchanged(): void
    {
        $file = $this->getFile();
        $uuid = $file->getFileUUID();
        $description = '<concrete-picture alt="A sample" file-id="' . $uuid . '" /> and <a href="{ccm:export:file::id=' . $uuid . '}">download</a>';
        $block = $this->addBlock();

        $this->updateBlock($block, ['entries' => [$this->getApiEntry('Files', $description)]]);

        // the references are resolved to the local file
        $db = $this->app->make(Connection::class);
        static::assertSame(
            '<concrete-picture fid="' . $file->getFileID() . '" alt="A sample" /> and <a href="{CCM:FID_DL_' . $uuid . '}">download</a>',
            $db->fetchOne('select description from btFaqEntries where bID = ?', [$block->getBlockID()])
        );
        // ... and they are exported back as they were received
        static::assertSame(
            $description,
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries'][0]['description']
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'faq';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        return $this->getSaveDataForEntries($this->getApiEntries());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        return array_merge($this->getMainApiValue(), ['entries' => $this->getApiEntries()]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getPartialApiValue()
     */
    protected function getPartialApiValue(): array
    {
        return ['blockTitle' => 'Other questions'];
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
     * Get the value of the columns of the btFaq table, as they are exchanged via the API.
     *
     * @return array<string,string>
     */
    private function getMainApiValue(): array
    {
        return ['blockTitle' => 'Questions'];
    }

    /**
     * Get a question of the FAQ, as it's exchanged via the API.
     *
     * @return array<string,string>
     */
    private function getApiEntry(string $title, string $description): array
    {
        return ['linkTitle' => mb_strtolower($title), 'title' => $title, 'description' => $description];
    }

    /**
     * Get the questions used by the tests.
     *
     * @return array[]
     */
    private function getApiEntries(): array
    {
        return [
            $this->getApiEntry('First', '<p>Hello</p>'),
            $this->getApiEntry('Second', '<p>World</p>'),
        ];
    }

    /**
     * Get the data to be passed to the save() method in order to create a FAQ with the given questions.
     *
     * @param array[] $entries
     *
     * @return array<string,mixed>
     */
    private function getSaveDataForEntries(array $entries): array
    {
        // the save() method wants the values of the questions in parallel arrays
        return array_merge($this->getMainApiValue(), [
            'linkTitle' => array_column($entries, 'linkTitle'),
            'title' => array_column($entries, 'title'),
            'description' => array_column($entries, 'description'),
            'sortOrder' => array_keys($entries),
        ]);
    }
}
