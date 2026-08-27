<?php

declare(strict_types=1);

namespace Concrete\Tests\Controller\SinglePage\Dashboard;

use Concrete\Controller\SinglePage\Dashboard\Users\Search;
use Concrete\Core\Http\Request;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Field\ManagerInterface;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Tests\TestCase;
use League\Url\Components\Query as UrlQuery;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

class UsersSearchTest extends TestCase
{
    public function testAdvancedSearchExportPreservesAllSelectedColumns(): void
    {
        $request = Request::create('/dashboard/users/search/advanced_search', 'GET', [
            'column' => ['u.uName', 'u.uEmail', 'u.uDateAdded'],
            'fSearchDefaultSort' => 'u.uName',
        ]);
        $reflection = new \ReflectionClass(TestableUsersSearchController::class);
        /** @var TestableUsersSearchController $controller */
        $controller = $reflection->newInstanceWithoutConstructor();
        $controller->setRequest($request);

        $parameters = $controller->getExportQueryParametersForTest();
        static::assertSame([
            'column' => ['u.uName', 'u.uEmail', 'u.uDateAdded'],
            'fSearchDefaultSort' => 'u.uName',
        ], $parameters);

        $exportQueryString = (string) new UrlQuery($parameters);
        parse_str($exportQueryString, $exportRequestParameters);
        static::assertSame($parameters, $exportRequestParameters);

        $availableColumns = new Set();
        foreach (['u.uName', 'u.uEmail', 'u.uDateAdded'] as $columnKey) {
            $availableColumns->addColumn(new Column($columnKey, $columnKey));
        }
        $fieldManager = M::mock(ManagerInterface::class);
        $fieldManager->shouldReceive('getFieldsFromRequest')->once()->with($parameters)->andReturn([]);
        $provider = M::mock(ProviderInterface::class);
        $provider->shouldReceive('getDefaultColumnSet')->once()->andReturn(new Set());
        $provider->shouldReceive('getBaseColumnSet')->once()->andReturn(new Set());
        $provider->shouldReceive('getAvailableColumnSet')->once()->andReturn($availableColumns);
        $provider->shouldReceive('getFieldManager')->once()->andReturn($fieldManager);

        $exportRequest = Request::create(
            '/dashboard/users/search/csv_export/advanced_search?' . $exportQueryString,
            'GET'
        );
        $exportQuery = (new QueryFactory())->createFromAdvancedSearchRequest(
            $provider,
            $exportRequest,
            Request::METHOD_GET
        );
        static::assertSame(
            ['u.uName', 'u.uEmail', 'u.uDateAdded'],
            array_map(static function (Column $column): string {
                return $column->getColumnKey();
            }, $exportQuery->getColumns()->getColumns())
        );
    }
}

class TestableUsersSearchController extends Search
{
    public function getExportQueryParametersForTest(): array
    {
        return $this->getExportQueryParameters();
    }
}
