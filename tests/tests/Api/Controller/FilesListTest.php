<?php

declare(strict_types=1);

namespace Concrete\Tests\Api\Controller;

use Concrete\Core\Api\Controller\Files;
use Concrete\Core\Api\Exception\InvalidLimitQueryParameterValueException;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use Concrete\Core\Permission\Category as PermissionCategory;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\User\Login\LoginService;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\TestHelpers\File\FileStorageTestCase;
use League\Fractal\Manager;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the endpoint listing the files (GET /ccm/api/1.0/files).
 */
class FilesListTest extends FileStorageTestCase
{
    /**
     * The files these tests work with, in the order they are created (that is, from the oldest to the newest).
     *
     * @var string[]
     */
    private const FILENAMES = ['first.txt', 'second.txt', 'third.txt', 'fourth.txt', 'fifth.txt'];

    /**
     * The ID of the user the endpoint is invoked as.
     *
     * @var int
     */
    private static $userID;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'AuthenticationTypes',
            'ConfigStore',
            'FilePermissionAssignments',
            'FileSetFiles',
            'FileSets',
            'FileVersionLog',
            'Logs',
            'Pages',
            'PermissionAccessEntities',
            'PermissionAccessEntityGroups',
            'PermissionAccessEntityTypes',
            'UserGroups',
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
            'Concrete\Core\Entity\Attribute\Category',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\File\Image\Thumbnail\Type\TypeFileSet',
            'Concrete\Core\Entity\User\User',
            'Concrete\Core\Entity\User\UserSignup',
        ]);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        app(Filesystem::class)->create();
        Category::add('file');
        PermissionAccessEntityType::add('file_uploader', 'File Uploader');
        PermissionCategory::add('file');
        PermissionKey::add('file', 'view_file_in_file_manager', 'View File in File Manager', '', 0, 0);

        AuthenticationType::add('concrete', 'Concrete');
        self::$userID = (int) UserInfo::add(['uName' => 'tester', 'uEmail' => 'tester\example.com'])->getUserID();
        app(LoginService::class)->loginByUserID(self::$userID);

        $self = new static();
        if (!is_dir($self->getStorageDirectory())) {
            mkdir($self->getStorageDirectory());
        }
        $self->getStorageLocation();
        $importer = app(FileImporter::class);
        $connection = app(Connection::class);
        $minutes = 0;
        foreach (self::FILENAMES as $filename) {
            $version = $importer->importLocalFile(DIR_TESTS . '/assets/File/StorageLocation/sample.txt', $filename);
            $dateAdded = sprintf('2026-01-01 00:%02d:00', $minutes++);
            $connection->executeStatement('update FileVersions set fvDateAdded = ? where fID = ?', [$dateAdded, $version->getFileID()]);
            $connection->executeStatement('update Files set fDateAdded = ? where fID = ?', [$dateAdded, $version->getFileID()]);
        }
        app(EntityManagerInterface::class)->clear();
    }

    public function testTheFilesAreListedFromTheNewestToTheOldest(): void
    {
        $data = $this->listFiles();

        static::assertSame(array_reverse(self::FILENAMES), $this->getTitles($data));
        static::assertSame(count(self::FILENAMES), $data['meta']['cursor']['count']);
        static::assertNull($data['meta']['cursor']['current']);
    }

    public function testTheNumberOfListedFilesCanBeLimited(): void
    {
        $data = $this->listFiles(['limit' => 2]);

        static::assertSame(['fifth.txt', 'fourth.txt'], $this->getTitles($data));
        static::assertSame(2, $data['meta']['cursor']['count']);
    }

    public static function providerInvalidLimits(): array
    {
        return [
            'zero' => ['0'],
            'negative' => ['-1'],
            'too big' => ['101'],
            'not a number' => ['some'],
            'empty' => [''],
        ];
    }

    /**
     * @dataProvider providerInvalidLimits
     */
    public function testInvalidLimitsAreRejected(string $limit): void
    {
        $this->expectException(InvalidLimitQueryParameterValueException::class);

        $this->listFiles(['limit' => $limit]);
    }

    public static function providerSearches(): array
    {
        return [
            'a part of the name shared by two files' => ['fi', ['fifth.txt', 'first.txt']],
            'the whole name of one file' => ['third.txt', ['third.txt']],
            'a part of the name of one file' => ['seco', ['second.txt']],
            'the extension shared by every file' => ['.txt', ['fifth.txt', 'fourth.txt', 'third.txt', 'second.txt', 'first.txt']],
            'a name no file has' => ['nothing like this', []],
            // the search is not case sensitive
            'the name of one file, in capitals' => ['FIRST', ['first.txt']],
            // an empty search is not a search
            'nothing' => ['', ['fifth.txt', 'fourth.txt', 'third.txt', 'second.txt', 'first.txt']],
            'blanks' => ['   ', ['fifth.txt', 'fourth.txt', 'third.txt', 'second.txt', 'first.txt']],
        ];
    }

    /**
     * @dataProvider providerSearches
     *
     * @param string[] $expectedFilenames
     */
    public function testTheFilesCanBeSearched(string $search, array $expectedFilenames): void
    {
        $data = $this->listFiles(['search' => $search]);

        static::assertSame($expectedFilenames, $this->getTitles($data));
        static::assertSame(count($expectedFilenames), $data['meta']['cursor']['count']);
    }

    public function testTheSearchIsLimitedToo(): void
    {
        $data = $this->listFiles(['search' => '.txt', 'limit' => 2]);

        static::assertSame(['fifth.txt', 'fourth.txt'], $this->getTitles($data));
    }

    public function testTheCursorWalksThroughTheSearchResults(): void
    {
        $firstPage = $this->listFiles(['search' => '.txt', 'limit' => 2]);

        $secondPage = $this->listFiles(['search' => '.txt', 'limit' => 2, 'after' => $firstPage['meta']['cursor']['next']]);

        static::assertSame(['third.txt', 'second.txt'], $this->getTitles($secondPage));
    }

    public function testTheCursorWalksThroughTheFiles(): void
    {
        $firstPage = $this->listFiles(['limit' => 2]);
        $cursor = $firstPage['meta']['cursor']['next'];
        static::assertNotNull($cursor);

        $secondPage = $this->listFiles(['limit' => 2, 'after' => $cursor]);

        static::assertSame(['third.txt', 'second.txt'], $this->getTitles($secondPage));
        static::assertSame($cursor, $secondPage['meta']['cursor']['current']);

        $lastPage = $this->listFiles(['limit' => 2, 'after' => $secondPage['meta']['cursor']['next']]);

        static::assertSame(['first.txt'], $this->getTitles($lastPage));

        // a page is always given a cursor: the end is reached when following it gives nothing back
        static::assertNotNull($lastPage['meta']['cursor']['next']);
        $afterTheLastPage = $this->listFiles(['limit' => 2, 'after' => $lastPage['meta']['cursor']['next']]);

        static::assertSame([], $afterTheLastPage['data']);
        static::assertNull($afterTheLastPage['meta']['cursor']['next']);
    }

    /**
     * The endpoint only lists the files the user may see in the file manager.
     */
    public function testTheFilesAreFilteredByPermissions(): void
    {
        app(User::class)->logout();
        try {
            $data = $this->listFiles();
        } finally {
            app(LoginService::class)->loginByUserID(self::$userID);
        }

        static::assertSame([], $data['data']);
        static::assertSame(0, $data['meta']['cursor']['count']);
    }

    /**
     * Get the payload the API would serialize.
     */
    private function listFiles(array $query = []): array
    {
        $controller = new Files(Request::create('/ccm/api/1.0/files', 'GET', $query));
        $controller->setApplication(app());

        return (new Manager())->createData($controller->listFiles())->toArray();
    }

    /**
     * @return string[]
     */
    private function getTitles(array $data): array
    {
        return array_column($data['data'], 'title');
    }
}
