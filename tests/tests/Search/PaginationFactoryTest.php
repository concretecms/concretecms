<?php

namespace Concrete\Tests\Search;

use Concrete\Core\Page\PageList;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PagerPagination;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Search\Pagination\PermissionablePagination;
use Concrete\TestHelpers\Search\TestList;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class PaginationFactoryTest extends PHPUnit_Framework_TestCase
{
    public static function getFakeQuery()
    {
        $db = \Database::connection();
        $builder = $db->createQueryBuilder();

        return $builder;
    }

    public static function getFakeAdapter()
    {
        $db = \Database::connection();
        $builder = $db->createQueryBuilder();
        $adapter = new DoctrineDbalAdapter($builder, function ($query) {
            // We need to reset the potential custom order by here because otherwise, if we've added
            // items to the select parts, and we're ordering by them, we get a SQL error
            // when we get total results, because we're resetting the select
            $query->select('count(foo)')->setMaxResults(1);
        });

        return $adapter;
    }

    public function testBasicPagination()
    {
        $request = Request::createFromGlobals();
        $list = new TestList();
        $factory = new PaginationFactory($request);
        $pagination = $factory->createPaginationObject($list);
        $this->assertInstanceOf(Pagination::class, $pagination);
    }

    public function testPageListNonPermissionedPagination()
    {
        $request = Request::createFromGlobals();
        $list = $this->getMockBuilder(PageList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $list->expects($this->once())
            ->method('getPermissionsChecker')
            ->willReturn(-1);
        $list->expects($this->once())
            ->method('getPaginationAdapter')
            ->willReturn(static::getFakeAdapter());

        $factory = new PaginationFactory($request);
        $pagination = $factory->createPaginationObject($list);
        $this->assertInstanceOf(Pagination::class, $pagination);
    }

    public function testPageListPermissionedPagination()
    {
        $request = Request::createFromGlobals();
        $list = $this->getMockBuilder(PageList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $list->expects($this->exactly(2))
            ->method('getQueryObject')
            ->willReturn(static::getFakeQuery());
        $list->expects($this->exactly(2))
            ->method('getResults')
            ->willReturn([]);

        $factory = new PaginationFactory($request);
        $pagination = $factory->createPaginationObject($list);
        $this->assertInstanceOf(PermissionablePagination::class, $pagination);

        $factory = new PaginationFactory($request);
        $pagination = $factory->createPaginationObject($list, PaginationFactory::PERMISSIONED_PAGINATION_STYLE_FULL);
        $this->assertInstanceOf(PermissionablePagination::class, $pagination);
    }

    public function testPageListPagerPermissionedPagination()
    {
        $request = Request::createFromGlobals();
        $list = $this->getMockBuilder(PageList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $list->expects($this->once())
            ->method('getPagerVariableFactory')
            ->willReturn(new VariableFactory($list));

        $factory = new PaginationFactory($request);
        $pagination = $factory->createPaginationObject($list, PaginationFactory::PERMISSIONED_PAGINATION_STYLE_PAGER);
        $this->assertInstanceOf(PagerPagination::class, $pagination);
    }
}
