<?php
/**
 * Created by PhpStorm.
 * User: andrewembler
 * Date: 1/27/15
 * Time: 6:24 AM
 */
use \Config;
use \Core;
class URLTest extends PHPUnit_Framework_TestCase
{
    /**
     * Here's the expected behavior.
     * All URLs generated should have index.php in front of them if
     * concrete.seo.url_rewriting is false.
     * If concrete.seo.url_rewriting is true, then all URLs should have
     * no index.php, unless they're in the Dashboard.
     * If concrete.seo.url_rewriting_all is true, then all URLs (including Dashboard)
     * should be free of index.php.
     *
     * This should be the case whether something is being called via URL::to, URL::page,
     * or Page::getCollectionLink or \Concrete\Core\Html\Service\Navigation::getLinkToCollection
     */

    public function setUp()
    {
        $service = Core::make('helper/navigation');
        $page = new Page();
        $page->cPath = '/path/to/my/page';
        $page->error = false;
        $dashboard = new Page();
        $dashboard->cPath = '/dashboard/my/awesome/page';
        $dashboard->error = false;
        $this->page = $page;
        $this->dashboard = $dashboard;
        $this->service = $service;
        Config::set('concrete.seo.url_rewriting', false);
        Config::set('concrete.seo.url_rewriting_all', false);
        Config::set('concrete.seo.canonical_host', false);

        parent::setUp();
    }

    public function testPathToSiteInApplication()
    {
        $this->assertEquals('/path/to/server', \Core::getApplicationRelativePath());
    }

    public function testNoUrlRewriting()
    {
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page',
            $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', URL::page($this->page));
    }

    public function testNoUrlRewritingNoRelativePath()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '';
        $app->instance('app', $app);

        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page',
            $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', URL::page($this->page));
    }

    public function testUrlRewriting()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '/path/to/server';
        $app->instance('app', $app);

        Config::set('concrete.seo.url_rewriting', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page',
            $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::page($this->page));
    }

    public function testUrlRewritingAll()
    {
        Config::set('concrete.seo.url_rewriting', true);
        Config::set('concrete.seo.url_rewriting_all', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page',
            $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::page($this->page));
    }

    public function testNoUrlRewritingDashboard()
    {
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',
            $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', URL::page($this->dashboard));
    }

    public function testPagesWithNoPaths()
    {
        $home = new Page();
        $home->cID = 1;
        $home->cPath = '';

        $url = \URL::to($home);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php', $url);

        $url = \URL::to('/');
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php', $url);

        $page = new Page();
        $page->cPath = null;
        $page->cID = 777;

        $url = \URL::to($page);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php?cID=777', $url);

    }

    public function testUrlRewritingDashboard()
    {
        Config::set('concrete.seo.url_rewriting', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',
            $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', URL::page($this->dashboard));
    }

    public function testUrlRewritingAllDashboard()
    {
        Config::set('concrete.seo.url_rewriting', true);
        Config::set('concrete.seo.url_rewriting_all', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page',
            $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', URL::page($this->dashboard));
    }

    public function testCanonicalHost()
    {
        Config::set('concrete.seo.canonical_host', 'www.derpco.com');
        $this->assertEquals('http://www.derpco.com/path/to/server/index.php/dashboard/my/awesome/page', URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.derpco.com/path/to/server/index.php/dashboard/my/awesome/page', URL::page($this->dashboard));
        Config::set('concrete.seo.canonical_host', false);
    }

    /*
    public function testPage()
    {

        // URL Rewriting
        Config::set('concrete.seo.url_rewriting', true);


        // URL Rewriting All
        Config::set('concrete.seo.url_rewriting_all', true);

        $this->assertEquals('/path/to/my/page', $c->getCollectionLink());
        $this->assertEquals('/path/to/my/page',
            $service->getLinkToCollection($c)
        );
        $this->assertEquals('/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('/path/to/my/page', URL::page($c));
    }
    */
}
