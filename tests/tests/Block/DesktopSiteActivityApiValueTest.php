<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that displays the activity of the site.
 *
 * @see \Concrete\Block\DesktopSiteActivity\Controller::getApiValueSchema()
 * @see \Concrete\Block\DesktopSiteActivity\Controller::serializeValueForApi()
 * @see \Concrete\Block\DesktopSiteActivity\Controller::getImportDataFromApiValue()
 */
class DesktopSiteActivityApiValueTest extends BlockApiValueTestCase
{
    public function testTheKindsAreReplacedWhenTheyAreSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'types' => ['workflow'],
        ]);

        static::assertSame(
            ['workflow'],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['types']
        );
    }

    public function testTheKindsAreDeletedWhenAnEmptyListIsSpecified(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'types' => [],
        ]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['types']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'desktop_site_activity';
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
            'types' => ['signups', 'form_submissions'],
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
            'types' => ['signups', 'form_submissions'],
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
}
