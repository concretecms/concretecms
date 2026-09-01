<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Database\Connection\Connection;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that shares the page with the social networks.
 *
 * @see \Concrete\Block\ShareThisPage\Controller::getApiValueSchema()
 * @see \Concrete\Block\ShareThisPage\Controller::serializeValueForApi()
 * @see \Concrete\Block\ShareThisPage\Controller::getImportDataFromApiValue()
 */
class ShareThisPageValueTest extends BlockApiValueTestCase
{
    public function testTheServicesKeepTheirOrder(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'services' => ['print', 'email', 'bluesky'],
        ]);

        static::assertSame(
            ['print', 'email', 'bluesky'],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['services']
        );
    }

    public function testTheServicesThatDontExistAreLeftOut(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'services' => ['email', 'the_social_network_of_another_package'],
        ]);

        static::assertSame(
            ['email'],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['services']
        );
    }

    public function testTheServicesCanBeDeleted(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'services' => [],
        ]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['services']);
        $db = $this->app->make(Connection::class);
        static::assertSame(
            0,
            (int) $db->fetchOne('select count(*) from btShareThisPage where bID = ?', [$block->getBlockID()])
        );
    }

    public function testTheSchemaListsTheAvailableServices(): void
    {
        $block = $this->addBlock();

        $schema = $this->app->make(ApiValueSchemaFactory::class)->getSchema($block->getController());

        static::assertContains('bluesky', $schema['properties']['services']['items']['enum']);
        static::assertContains('email', $schema['properties']['services']['items']['enum']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'share_this_page';
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
            'service' => ['bluesky', 'email'],
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
            'services' => ['bluesky', 'email'],
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
