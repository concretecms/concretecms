<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Sharing\SocialNetwork\Link as SocialLink;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that displays the social links of the site.
 *
 * @see \Concrete\Block\SocialLinks\Controller::getApiValueSchema()
 * @see \Concrete\Block\SocialLinks\Controller::serializeValueForApi()
 * @see \Concrete\Block\SocialLinks\Controller::getImportDataFromApiValue()
 */
class SocialLinksValueTest extends BlockApiValueTestCase
{
    /**
     * The social links of the site, by the handle of their service.
     *
     * @var \Concrete\Core\Entity\Sharing\SocialNetwork\Link[]
     */
    private $links = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            SocialLink::class,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->links = [];
    }

    public function testTheLinksKeepTheirOrder(): void
    {
        $this->createLink('bluesky');
        $this->createLink('github');
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'links' => ['github', 'bluesky'],
        ]);

        static::assertSame(
            ['github', 'bluesky'],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['links']
        );
    }

    public function testTheLinksThatTheSiteDoesntHaveAreLeftOut(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'links' => ['github', 'the_social_network_of_another_site'],
        ]);

        static::assertSame(
            ['github'],
            $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['links']
        );
    }

    public function testTheLinksCanBeDeleted(): void
    {
        $block = $this->addBlock();

        $this->updateBlock($block, [
            'links' => [],
        ]);

        static::assertSame([], $this->getApiValue($this->getBlock($block->getBlockCollectionObject()))['links']);
        $db = $this->app->make(Connection::class);
        static::assertSame(
            0,
            (int) $db->fetchOne('select count(*) from btSocialLinks where bID = ?', [$block->getBlockID()])
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'social_links';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends: it refers to the links by their ID
        return [
            'slID' => [$this->createLink('bluesky'), $this->createLink('github')],
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
            'links' => ['bluesky', 'github'],
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

    /**
     * Add a social link to the site (it's created the first time it's asked for).
     *
     * @return int the ID of the link
     */
    private function createLink(string $serviceHandle): int
    {
        if (!isset($this->links[$serviceHandle])) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $site = $this->app->make('site')->getSite();
            // the tests of a class share the tables: the link may have been created by another one
            $link = $entityManager->getRepository(SocialLink::class)->findOneBy(['site' => $site, 'ssHandle' => $serviceHandle]);
            if ($link === null) {
                $link = new SocialLink();
                $link->setServiceHandle($serviceHandle);
                $link->setSite($site);
                $link->setURL("https://www.example.com/{$serviceHandle}");
                $entityManager->persist($link);
                $entityManager->flush();
            }
            $this->links[$serviceHandle] = $link;
        }

        return (int) $this->links[$serviceHandle]->getID();
    }
}
