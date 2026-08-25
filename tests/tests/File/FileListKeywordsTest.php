<?php

declare(strict_types=1);

namespace Concrete\Tests\File;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\FileKey as FileKeyEntity;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;
use Concrete\TestHelpers\File\FileStorageTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the keyword filtering of the file list.
 *
 * @see \Concrete\Core\File\FileList::filterByKeywords()
 */
class FileListKeywordsTest extends FileStorageTestCase
{
    /**
     * The name of the user that uploads the files.
     *
     * @var string
     */
    private const UPLOADER = 'admin';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'ConfigStore',
            'FileSets',
            'FileSetFiles',
            'FileVersionLog',
            'Logs',
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
            'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
            'Concrete\Core\Entity\File\Image\Thumbnail\Type\TypeFileSet',
            'Concrete\Core\Entity\User\User',
        ]);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        app(Filesystem::class)->create();
        $category = Category::add('file');
        PermissionAccessEntityType::add('file_uploader', 'File Uploader');

        // a searchable attribute: the keywords are looked for in the indexed attributes too
        $attributeKey = new FileKeyEntity();
        $attributeKey->setAttributeKeyHandle('notes');
        $attributeKey->setAttributeKeyName('Notes');
        $attributeKey->setIsAttributeKeyContentIndexed(true);
        $category->add(AttributeType::add('text', 'Text'), $attributeKey);

        if (!is_dir(static::getStorageDirectory())) {
            mkdir(static::getStorageDirectory());
        }
        static::getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $importer = app(FileImporter::class);
        $version = $importer->importLocalFile($sample, 'recipe-1.txt');
        $version->updateTitle('Pizzoccheri della Valtellina');
        $version = $importer->importLocalFile($sample, 'recipe-2.txt');
        $version->updateTitle('Risotto allo zafferano');
        $version->updateDescription('A dish from Milan');
        $version = $importer->importLocalFile($sample, 'shopping-list.txt');
        $version->updateTags("cheese\nbutter");
        $version->getFile()->setAttribute('notes', 'Ask for gnocchi');

        // the files must belong to a user, so that they can be searched by the name of who uploaded them
        $user = new UserEntity();
        $user->setUserName(self::UPLOADER);
        $user->setUserEmail('admin@example.com');
        $user->setUserPassword('');
        $user->setUserDateAdded(new \DateTime());
        $user->setUserLastPasswordChange(new \DateTime());
        $entityManager = app(EntityManagerInterface::class);
        $entityManager->persist($user);
        $entityManager->flush();
        app(Connection::class)->executeStatement('update Files set uID = ?', [$user->getUserID()]);
    }

    public static function providerKeywords(): array
    {
        return [
            // a word is looked for in every searchable field
            'a part of a title' => ['pizzoccher', ['recipe-1.txt']],
            'a part of a file name' => ['recipe', ['recipe-1.txt', 'recipe-2.txt']],
            'a part of a description' => ['Milan', ['recipe-2.txt']],
            'a tag' => ['butter', ['shopping-list.txt']],
            'a word of a searchable attribute' => ['gnocchi', ['shopping-list.txt']],
            'an any-characters wildcard' => ['%', []],
            'a one-character wildcard' => ['_', []],
            'a wildcard in a word' => ['recipe_1', []],
            'an attribute and a file name' => ['gnocchi shopping', ['shopping-list.txt']],
            'an attribute and another file' => ['gnocchi recipe', []],
            'the name of the uploader' => [self::UPLOADER, ['recipe-1.txt', 'recipe-2.txt', 'shopping-list.txt']],
            // every word must be found, each one in any field
            'the uploader and a part of a title' => ['admin pizzoccher', ['recipe-1.txt']],
            'a file name and a title' => ['recipe risotto', ['recipe-2.txt']],
            'two words of the same title' => ['della pizzoccheri', ['recipe-1.txt']],
            'words found in different files' => ['pizzoccheri risotto', []],
            'a word no file has' => ['pizzoccheri lasagna', []],
            // the words are normalized
            'the same word twice' => ['risotto Risotto', ['recipe-2.txt']],
            'a title in capitals' => ['RISOTTO', ['recipe-2.txt']],
            'blanks between the words' => ["  recipe \t risotto  ", ['recipe-2.txt']],
            // an empty search is not a search
            'nothing' => ['', ['recipe-1.txt', 'recipe-2.txt', 'shopping-list.txt']],
            'blanks' => ["  \t ", ['recipe-1.txt', 'recipe-2.txt', 'shopping-list.txt']],
        ];
    }

    /**
     * @dataProvider providerKeywords
     *
     * @param string[] $expectedFilenames
     */
    public function testFilterByKeywords(string $keywords, array $expectedFilenames): void
    {
        $list = new FileList();
        $list->ignorePermissions();
        $list->filterByKeywords($keywords);

        static::assertSame($expectedFilenames, $this->getFilenames($list));
    }

    public function testFilterByKeywordsCalledMoreThanOnce(): void
    {
        $list = new FileList();
        $list->ignorePermissions();
        $list->filterByKeywords('recipe');
        $list->filterByKeywords('admin');

        static::assertSame(['recipe-1.txt', 'recipe-2.txt'], $this->getFilenames($list));

        $list = new FileList();
        $list->ignorePermissions();
        $list->filterByKeywords('pizzoccheri');
        $list->filterByKeywords('risotto');

        static::assertSame([], $this->getFilenames($list));
    }

    /**
     * @return string[]
     */
    private function getFilenames(FileList $list): array
    {
        $result = [];
        foreach ($list->getResults() as $file) {
            $result[] = $file->getApprovedVersion()->getFileName();
        }
        sort($result);

        return $result;
    }
}