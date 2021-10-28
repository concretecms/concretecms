<?php

namespace Concrete\Tests\Navigation;

use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Page\Page;
use Concrete\Tests\TestCase;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumbFactory;
use Mockery as M;

class DashboardBreadcrumbNavigationTest extends TestCase
{


    public function testCreateNavigation()
    {
        $data = [
            '/dashboard/system/attributes' => 'Attributes',
            '/dashboard/system' => 'System & Settings',
            '/dashboard' => 'Dashboard',
        ];
        $breadcrumbPage = M::mock(Page::class);
        $breadcrumbPage->shouldReceive('getCollectionLink')->andReturn('/dashboard/system/attributes/types');
        $breadcrumbPage->shouldReceive('getCollectionName')->andReturn('Types');
        $pages = [];
        foreach($data as $path => $name) {
            $page = M::mock(Page::class);
            $page->shouldReceive('getCollectionLink')->andReturn($path);
            $page->shouldReceive('getCollectionName')->andReturn($name);
            $pages[] = $page;
        }
        $navigation = M::mock(Navigation::class);
        $navigation->shouldReceive('getTrailToCollection')->andReturn($pages);
        $factory = new DashboardBreadcrumbFactory($navigation);
        $breadcrumb = $factory->getBreadcrumb($breadcrumbPage);
    }

}
