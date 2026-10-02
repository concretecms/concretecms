<?php

declare(strict_types=1);

namespace Concrete\Tests\Csv\Import;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Csv\Import\ImportResult;
use Concrete\Core\Error\ErrorList\Error\ErrorInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\TestHelpers\Csv\Import\TestImporter;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class AbstractImporterTest extends TestCase
{
    public function testAllTheDataRowsAreProcessed(): void
    {
        $importer = $this->createImporter("name\nfirst\nsecond\nthird\n");
        $result = $importer->process();

        static::assertSame(['first', 'second', 'third'], $importer->importedNames);
        static::assertSame(3, $result->getTotalDataRowsProcessed());
        static::assertSame(3, $result->getImportSuccessCount());
        static::assertSame(3, $result->getLastDataRowIndex());
        static::assertSame([], self::getMessages($result->getErrors()));
        static::assertSame([], self::getMessages($result->getWarnings()));
    }

    public function testDataRowsCanBeSkippedAndLimited(): void
    {
        $importer = $this->createImporter("name\nfirst\nsecond\nthird\nfourth\n");
        $result = $importer->process(1, 2);

        static::assertSame(['second', 'third'], $importer->importedNames);
        static::assertSame(2, $result->getTotalDataRowsProcessed());
        static::assertSame(3, $result->getLastDataRowIndex());
    }

    public function testProblemsReferToTheCsvLines(): void
    {
        $importer = $this->createImporter("name\nfirst\n\ninvalid\nlast\n");
        $result = $importer->process();

        static::assertSame(['first', 'last'], $importer->importedNames);
        static::assertSame(3, $result->getTotalDataRowsProcessed());
        static::assertSame(2, $result->getImportSuccessCount());
        static::assertSame(['Line #4: Invalid name'], self::getMessages($result->getErrors()));
    }

    public function testUnrecognizedHeaders(): void
    {
        $importer = $this->createImporter("foo\nbar\n");
        $result = $importer->process();

        static::assertSame([], $importer->importedNames);
        static::assertSame(0, $result->getTotalDataRowsProcessed());
        static::assertSame(['Line #1: None of the CSV columns have been recognized.'], self::getMessages($result->getErrors()));
    }

    public function testEmptyCsv(): void
    {
        $importer = $this->createImporter('');
        $result = $importer->process();

        static::assertSame([], $importer->importedNames);
        static::assertSame(0, $result->getTotalDataRowsProcessed());
        static::assertSame(["There's no row in the CSV."], self::getMessages($result->getErrors()));
    }

    private function createImporter(string $csv): TestImporter
    {
        $app = M::mock(Application::class);
        $app->shouldReceive('make')->with(EntityManagerInterface::class)->andReturn(M::mock(EntityManagerInterface::class));
        $app->shouldReceive('make')->with(ImportResult::class)->andReturnUsing(static function () {
            return new ImportResult(new ErrorList(), new ErrorList());
        });
        $app->shouldReceive('build')->with(ErrorList::class)->andReturnUsing(static function () {
            return new ErrorList();
        });
        $category = M::mock(CategoryInterface::class);
        $category->shouldReceive('getList')->andReturn([]);

        return new TestImporter(Reader::createFromString($csv), $category, $app);
    }

    /**
     * @return string[]
     */
    private static function getMessages(ErrorList $errorList): array
    {
        return array_map(
            static function (ErrorInterface $error): string {
                return (string) $error->getMessage();
            },
            $errorList->getList()
        );
    }
}
