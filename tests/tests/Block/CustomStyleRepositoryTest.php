<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\CustomStyleRepository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Utility\Service\Text;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Every version of a page has the custom styles of its blocks, and the repository that reads them lives
 * for the whole request.
 *
 * @see \Concrete\Core\Block\CustomStyleRepository
 */
class CustomStyleRepositoryTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'CollectionVersionBlockStyles',
    ];

    public function testEachVersionOfAPageHandsOverTheStyleItsBlockHasThere(): void
    {
        $this->giveTheBlockAStyle(10, 1, 'Main', 5, 11);
        $this->giveTheBlockAStyle(10, 2, 'Main', 5, 22);
        $repository = $this->createRepository();

        static::assertSame(11, $repository->getBlockStyleID($this->createBlock(10, 1, 'Main', 5)));
        static::assertSame(22, $repository->getBlockStyleID($this->createBlock(10, 2, 'Main', 5)));
    }

    /**
     * The version read first used to answer for the other one as well.
     */
    public function testTheOrderTheVersionsAreReadInChangesNothing(): void
    {
        $this->giveTheBlockAStyle(20, 1, 'Main', 5, 11);
        $this->giveTheBlockAStyle(20, 2, 'Main', 5, 22);
        $repository = $this->createRepository();

        static::assertSame(22, $repository->getBlockStyleID($this->createBlock(20, 2, 'Main', 5)));
        static::assertSame(11, $repository->getBlockStyleID($this->createBlock(20, 1, 'Main', 5)));
    }

    public function testABlockWithNoStyleInThatVersionHasNone(): void
    {
        $this->giveTheBlockAStyle(30, 1, 'Main', 5, 11);
        $repository = $this->createRepository();

        static::assertNull($repository->getBlockStyleID($this->createBlock(30, 2, 'Main', 5)));
        static::assertSame(11, $repository->getBlockStyleID($this->createBlock(30, 1, 'Main', 5)));
    }

    private function createRepository(): CustomStyleRepository
    {
        // one of its own, since the one of the application lives as long as the request
        return new CustomStyleRepository(app(Connection::class), app(Text::class));
    }

    private function createBlock(int $collectionID, int $versionID, string $areaHandle, int $blockID): Block
    {
        $collection = $this->createMock(Collection::class);
        $collection->method('getCollectionID')->willReturn($collectionID);
        $collection->method('getVersionID')->willReturn($versionID);
        $area = $this->createMock(Area::class);
        $area->method('isGlobalArea')->willReturn(false);
        $block = $this->createMock(Block::class);
        $block->method('getBlockCollectionObject')->willReturn($collection);
        $block->method('getBlockAreaObject')->willReturn($area);
        $block->method('getAreaHandle')->willReturn($areaHandle);
        $block->method('getBlockID')->willReturn($blockID);

        return $block;
    }

    private function giveTheBlockAStyle(int $collectionID, int $versionID, string $areaHandle, int $blockID, int $styleSetID): void
    {
        app(Connection::class)->insert('CollectionVersionBlockStyles', [
            'cID' => $collectionID,
            'cvID' => $versionID,
            'arHandle' => $areaHandle,
            'bID' => $blockID,
            'issID' => $styleSetID,
        ]);
    }
}
