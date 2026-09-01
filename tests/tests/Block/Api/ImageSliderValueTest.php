<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the image slider block.
 *
 * @see \Concrete\Block\ImageSlider\Controller::getApiValueSchema()
 * @see \Concrete\Block\ImageSlider\Controller::serializeValueForApi()
 * @see \Concrete\Block\ImageSlider\Controller::getImportDataFromApiValue()
 */
class ImageSliderValueTest extends BlockApiValueTestCase
{
    /**
     * The page that the slides can link to.
     *
     * @var \Concrete\Core\Page\Page|null
     */
    private $linkedPage;

    public function setUp(): void
    {
        parent::setUp();
        $this->linkedPage = null;
    }

    public function testTheSlidesAreReplacedWhenTheyAreSpecified(): void
    {
        $block = $this->addBlock();
        $entries = [$this->getApiEntry('Another slide')];

        $this->updateBlock($block, ['entries' => $entries]);

        static::assertSame($entries, $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    public function testTheSlidesAreDeletedWhenAnEmptyListIsSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, ['entries' => []]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    public function testASlideLinksToAnUrlWhenItDoesntLinkToAPage(): void
    {
        $block = $this->addBlock();
        $entry = [
            'fID' => '0',
            'linkURL' => 'https://www.example.com',
            'internalLinkCID' => '0',
            'title' => 'Linking elsewhere',
            'description' => '',
        ];

        $this->updateBlock($block, ['entries' => [$entry]]);

        static::assertSame([$entry], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    public function testTheLinkToAPageWinsOverTheLinkToAnUrl(): void
    {
        $block = $this->addBlock();
        $entry = $this->getApiEntry('Linking to a page');
        $entry['linkURL'] = 'https://www.example.com';

        $this->updateBlock($block, ['entries' => [$entry]]);

        $entry['linkURL'] = '';
        static::assertSame([$entry], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['entries']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'image_slider';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        $entries = $this->getApiEntries();

        // the save() method wants the values of the slides in parallel arrays
        return array_merge($this->getMainApiValue(), [
            'fID' => [$this->getFile()->getFileID()],
            'title' => array_column($entries, 'title'),
            'description' => array_column($entries, 'description'),
            'linkURL' => array_column($entries, 'linkURL'),
            'internalLinkCID' => [$this->getLinkedPage()->getCollectionID()],
            'linkType' => [1],
            'sortOrder' => array_keys($entries),
        ]);
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
        return ['maxWidth' => '800'];
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
     * Get the value of the columns of the btImageSlider table, as they are exchanged via the API.
     *
     * @return array<string,string>
     */
    private function getMainApiValue(): array
    {
        return [
            'navigationType' => '2',
            'timeout' => '4000',
            'speed' => '500',
            'noAnimate' => '0',
            'pause' => '1',
            'maxWidth' => '0',
        ];
    }

    /**
     * Get a slide, as it's exchanged via the API.
     *
     * @return array<string,string>
     */
    private function getApiEntry(string $title): array
    {
        // the keys are in the order of the columns of the btImageSliderEntries table
        return [
            'fID' => $this->getFile()->getFileUUID(),
            'linkURL' => '',
            'internalLinkCID' => (string) $this->getLinkedPage()->getCollectionID(),
            'title' => $title,
            'description' => '<p>The description of ' . $title . '</p>',
        ];
    }

    /**
     * Get the slides used by the tests.
     *
     * @return array[]
     */
    private function getApiEntries(): array
    {
        return [$this->getApiEntry('The only slide')];
    }

    /**
     * Get the page that the slides link to.
     */
    private function getLinkedPage(): Page
    {
        if ($this->linkedPage === null) {
            $this->linkedPage = self::createPage('Linked page');
        }

        return $this->linkedPage;
    }
}
