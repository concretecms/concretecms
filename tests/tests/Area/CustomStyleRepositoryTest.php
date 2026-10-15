<?php

declare(strict_types=1);

namespace Concrete\Tests\Area;

use Concrete\Core\Area\CustomStyleRepository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Utility\Service\Text;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Every version of a page has custom styles of its own, and the repository that reads them lives for
 * the whole request.
 *
 * @see \Concrete\Core\Area\CustomStyleRepository
 */
class CustomStyleRepositoryTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'CollectionVersionAreaStyles',
    ];

    public function testEachVersionOfAPageHandsOverTheStylesOfItsOwn(): void
    {
        $this->giveTheAreaAStyle(10, 1, 'Main', 11);
        $this->giveTheAreaAStyle(10, 2, 'Main', 22);
        $this->giveTheAreaAStyle(10, 2, 'Sidebar', 33);
        $repository = $this->createRepository();

        static::assertSame(['Main' => 11], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(10, 1)));
        static::assertSame(['Main' => 22, 'Sidebar' => 33], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(10, 2)));
    }

    /**
     * The one read first used to answer for the other one as well.
     */
    public function testTheOrderTheVersionsAreReadInChangesNothing(): void
    {
        $this->giveTheAreaAStyle(20, 1, 'Main', 11);
        $this->giveTheAreaAStyle(20, 2, 'Main', 22);
        $repository = $this->createRepository();

        static::assertSame(['Main' => 22], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(20, 2)));
        static::assertSame(['Main' => 11], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(20, 1)));
    }

    public function testAVersionWithNoStyleOfItsOwnHandsOverNone(): void
    {
        $this->giveTheAreaAStyle(30, 1, 'Main', 11);
        $repository = $this->createRepository();

        static::assertSame([], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(30, 2)));
        static::assertSame(['Main' => 11], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(30, 1)));
    }

    public function testAVersionThatIsNotThereYetHandsOverNone(): void
    {
        $repository = $this->createRepository();

        static::assertSame([], $repository->getCollectionVersionAreaStyleIDs($this->createVersion(40, null)));
    }

    private function createRepository(): CustomStyleRepository
    {
        // one of its own, since the one of the application lives as long as the request
        return new CustomStyleRepository(app(Connection::class), app(Text::class));
    }

    private function createVersion(int $collectionID, ?int $versionID): Version
    {
        $version = $this->createMock(Version::class);
        $version->method('getCollectionID')->willReturn($collectionID);
        $version->method('getVersionID')->willReturn($versionID);

        return $version;
    }

    private function giveTheAreaAStyle(int $collectionID, int $versionID, string $areaHandle, int $styleSetID): void
    {
        app(Connection::class)->insert('CollectionVersionAreaStyles', [
            'cID' => $collectionID,
            'cvID' => $versionID,
            'arHandle' => $areaHandle,
            'issID' => $styleSetID,
        ]);
    }
}
