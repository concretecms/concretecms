<?php

declare(strict_types=1);

namespace Concrete\Tests\Csv\Export;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Csv\Export\UserExporter;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\ColumnExportableInterface;
use Concrete\Core\Search\Column\ColumnInterface;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\User\UserInfo;
use Concrete\Tests\TestCase;
use League\Csv\Writer;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class UserExporterTest extends TestCase
{
    private $headers = [];

    private $rows = [];

    public function testSelectedColumnsAreExportedInOrderForEveryResult(): void
    {
        $firstUser = M::mock(UserInfo::class);
        $secondUser = M::mock(UserInfo::class);

        $email = M::mock(ColumnInterface::class);
        $email->shouldReceive('getColumnName')->once()->andReturn('Email');
        $email->shouldReceive('getColumnValue')->with($firstUser)->once()
            ->andReturn('<a href="mailto:first@example.com">first@example.com</a>')
        ;
        $email->shouldReceive('getColumnValue')->with($secondUser)->once()
            ->andReturn('<a href="mailto:second@example.com">second@example.com</a>')
        ;

        $groups = M::mock(ColumnInterface::class);
        $groups->shouldReceive('getColumnName')->once()->andReturn('Groups');
        $groups->shouldReceive('getColumnValue')->with($firstUser)->once()
            ->andReturn('<span class="badge">Administrators</span><span class="badge">Editors</span>')
        ;
        $groups->shouldReceive('getColumnValue')->with($secondUser)->once()->andReturn('Editors');

        $columns = new Set();
        $columns->addColumn($email);
        $columns->addColumn($groups);

        $exporter = $this->createExporter($columns);
        $list = new TestUserList([$firstUser, $secondUser]);
        $list->setItemsPerPage(1);

        $exporter->insertHeaders();
        $exporter->insertList($list);

        static::assertSame(['Email', 'Groups'], $this->headers);
        static::assertSame([
            ['first@example.com', 'Administrators; Editors'],
            ['second@example.com', 'Editors'],
        ], $this->rows);
    }

    public function testColumnSpecificExportValueTakesPrecedence(): void
    {
        $user = M::mock(UserInfo::class);
        $column = M::mock(ColumnInterface::class, ColumnExportableInterface::class);
        $column->shouldReceive('getColumnName')->once()->andReturn('Membership Paths');
        $column->shouldReceive('getColumnExportValue')->with($user)->once()->andReturn([
            'Staff > Editors',
            'Partners > Resellers > Europe',
        ]);
        $column->shouldNotReceive('getColumnValue');

        $columns = new Set();
        $columns->addColumn($column);

        $exporter = $this->createExporter($columns);
        $exporter->insertHeaders();
        $exporter->insertObject($user);

        static::assertSame(['Membership Paths'], $this->headers);
        static::assertSame([
            ['Staff > Editors; Partners > Resellers > Europe'],
        ], $this->rows);
    }

    public function testAttributeColumnsUseTheirPlainTextValue(): void
    {
        $attributeKey = M::mock();
        $attributeKey->shouldReceive('getAttributeKeyHandle')->andReturn('bio');
        $attributeKey->shouldReceive('getAttributeKeyDisplayName')->andReturn('Biography');

        $attributeValue = M::mock();
        $attributeValue->shouldReceive('getPlainTextValue')->once()->andReturn('Developer & Editor');
        $attributeValue->shouldNotReceive('getDisplayValue');

        $user = M::mock(UserInfo::class);
        $user->shouldReceive('getAttributeValueObject')->with($attributeKey)->once()->andReturn($attributeValue);

        $columns = new Set();
        $columns->addColumn(new AttributeKeyColumn($attributeKey));

        $exporter = $this->createExporter($columns);
        $exporter->insertHeaders();
        $exporter->insertObject($user);

        static::assertSame(['Biography'], $this->headers);
        static::assertSame([['Developer & Editor']], $this->rows);
    }

    public function testLegacyFormatRemainsAvailableWithoutAColumnSet(): void
    {
        $user = M::mock(UserInfo::class);
        $user->shouldReceive('getUserID')->once()->andReturn(12);
        $user->shouldReceive('getUserName')->once()->andReturn('jdoe');
        $user->shouldReceive('getUserEmail')->once()->andReturn('jdoe@example.com');
        $user->shouldReceive('getUserDateAdded')->once()->andReturn(new \DateTimeImmutable('2024-01-02T03:04:05+00:00'));
        $user->shouldReceive('isActive')->once()->andReturn(true);
        $user->shouldReceive('getNumLogins')->once()->andReturn(7);
        $user->shouldReceive('getAttributeValueObject')->never();

        $exporter = $this->createExporter();
        $exporter->insertHeaders();
        $exporter->insertObject($user);

        static::assertSame(['id', 'username', 'email', 'dateAdded', 'active', 'numLogins'], $this->headers);
        static::assertSame([
            ['12', 'jdoe', 'jdoe@example.com', '2024-01-02T03:04:05+00:00', '1', '7'],
        ], $this->rows);
    }

    private function createExporter(?Set $columns = null): UserExporter
    {
        $writer = M::mock(Writer::class);
        $writer->shouldReceive('insertOne')->andReturnUsing(function (iterable $values): int {
            $values = iterator_to_array((static function () use ($values) {
                yield from $values;
            })());
            if ($this->headers === []) {
                $this->headers = $values;
            } else {
                $this->rows[] = $values;
            }

            return 1;
        });
        $writer->shouldReceive('insertAll')->andReturnUsing(function (iterable $values): int {
            foreach ($values as $value) {
                $this->rows[] = $value;
            }

            return count($this->rows);
        });

        $userCategory = M::mock(UserCategory::class);
        if ($columns === null) {
            $userCategory->shouldReceive('getList')->andReturn([]);
        }

        $dateService = M::mock(Date::class);
        $dateService->shouldReceive('getTimezone')->with('app')->andReturn(new \DateTimeZone('UTC'));
        $dateService->shouldReceive('formatCustom')->andReturnUsing(
            static function (string $format, \DateTimeInterface $date): string {
                return $date->format($format);
            }
        );

        $config = M::mock(Repository::class);
        $config->shouldReceive('get')->with('concrete.export.csv.datetime_format', 'ATOM')->andReturn('ATOM');

        return new UserExporter($writer, $userCategory, $dateService, $config, $columns);
    }

    protected function tearDown(): void
    {
        $this->headers = [];
        $this->rows = [];
        parent::tearDown();
    }
}

class TestUserList extends ItemList
{
    private $results;

    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function createQuery()
    {
    }

    public function deliverQueryObject()
    {
        return new TestUserListQuery($this->results);
    }

    public function getResult($mixed)
    {
        return $mixed;
    }

    public function getTotalResults()
    {
        return count($this->results);
    }
}

class TestUserListQuery
{
    private $results;

    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function execute(): array
    {
        return $this->results;
    }
}
