<?php
/**
 * Created by PhpStorm.
 * User: andrewembler
 * Date: 1/27/15
 * Time: 6:24 AM
 */

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
        Config::set('concrete.seo.canonical_url', false);

        parent::setUp();
    }

    public function tearDown()
    {
        \Core::forgetInstance('url/canonical');
        parent::tearDown();
    }

    public function testPathToSiteInApplication()
    {
        $this->assertEquals('/path/to/server', \Core::getApplicationRelativePath());
    }

    public function testNoUrlRewriting()
    {
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', (string) URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', (string) URL::page($this->page));
    }

    public function testNoUrlRewritingNoRelativePath()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '';
        $app->instance('app', $app);

        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', (string) URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', (string) URL::page($this->page));
    }

    public function testUrlRewriting()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '/path/to/server';
        $app->instance('app', $app);

        Config::set('concrete.seo.url_rewriting', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) URL::page($this->page));
    }

    public function testCanonicalURLRedirection()
    {
        $app = Core::make("app");
        Config::set('concrete.seo.redirect_to_canonical_url', true);
        Config::set('concrete.seo.canonical_url', 'https://www2.myawesomesite.com:8080');
        Config::set('concrete.seo.canonical_ssl_url', 'https://www2.myawesomesite.com:8080');
        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/path/to/site/index.php/dashboard?bar=1&foo=1');
        $response = $app->handleCanonicalURLRedirection($request);

        $this->assertEquals('https://www2.myawesomesite.com:8080/path/to/site/index.php/dashboard?bar=1&foo=1', $response->getTargetUrl());
        Config::set('concrete.seo.redirect_to_canonical_url', false);
        Config::set('concrete.seo.canonical_url', null);
    }

    public function testCanonicalURLRedirectionSameDomain()
    {
        $app = Core::make("app");
        Config::set('concrete.seo.redirect_to_canonical_url', true);
        Config::set('concrete.seo.canonical_url', 'http://concrete5.dev');
        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/login');
        $response = $app->handleCanonicalURLRedirection($request);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/index.php?cID=1');
        $response = $app->handleCanonicalURLRedirection($request);
        $this->assertNull($response);

        Config::set('concrete.seo.redirect_to_canonical_url', false);
        Config::set('concrete.seo.canonical_url', null);
    }

    public function testCanonicalUrlRedirectionSslUrl()
    {
        $app = Core::make("app");
        Config::set('concrete.seo.redirect_to_canonical_url', true);
        Config::set('concrete.seo.canonical_url', 'http://mysite.com');
        Config::set('concrete.seo.canonical_ssl_url', 'https://secure.mysite.com:8080');
        $request = \Concrete\Core\Http\Request::create('https://secure.mysite.com:8080/path/to/page');
        $response = $app->handleCanonicalURLRedirection($request);
        $this->assertNull($response);
        Config::set('concrete.seo.redirect_to_canonical_url', false);
        Config::set('concrete.seo.canonical_url', null);
        Config::set('concrete.seo.canonical_ssl_url', null);
    }

    public function testPathSlashesRedirection()
    {
        $app = Core::make("app");




        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/derp');
        $response = $app->handleURLSlashes($request);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/index.php?cID=1');
        $response = $app->handleURLSlashes($request);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/about-us/now');
        $response = $app->handleURLSlashes($request);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/about-us/now/');
        $response = $app->handleURLSlashes($request);
        $this->assertInstanceOf('\Concrete\Core\Routing\RedirectResponse', $response);
        $this->assertEquals('http://www.awesome.com/about-us/now', $response->getTargetUrl());

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/index.php/about-us/now/?bar=1&foo=2');
        $response = $app->handleURLSlashes($request);
        $this->assertInstanceOf('\Concrete\Core\Routing\RedirectResponse', $response);
        $this->assertEquals('http://www.awesome.com/index.php/about-us/now?bar=1&foo=2', $response->getTargetUrl());

        Config::set('concrete.seo.trailing_slash', true);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com:8080/index.php/about-us/now/?bar=1&foo=2');
        $response = $app->handleURLSlashes($request);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com:8080/index.php/about-us/now?bar=1&foo=2');
        $response = $app->handleURLSlashes($request);
        $this->assertEquals('http://www.awesome.com:8080/index.php/about-us/now/?bar=1&foo=2', $response->getTargetUrl());

        Config::set('concrete.seo.trailing_slash', false);
    }

    public function testUrlRewritingAll()
    {
        Config::set('concrete.seo.url_rewriting', true);
        Config::set('concrete.seo.url_rewriting_all', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::page($this->page));
    }

    public function testNoUrlRewritingDashboard()
    {
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',
                            (string) $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',(string)  URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',(string)  URL::page($this->dashboard));
    }

    public function testPagesWithNoPaths()
    {
        $home = new Page();
        $home->cID = 1;
        $home->cPath = '';




        $url = \URL::to($home);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php', (string) $url);

        $url = \URL::to('/');
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php', (string) $url);

        $page = new Page();
        $page->cPath = null;
        $page->cID = 777;

        $url = \URL::to($page);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php?cID=777', (string) $url);

    }

    public function testUrlRewritingDashboard()
    {
        Config::set('concrete.seo.url_rewriting', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',
                            (string) $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testUrlRewritingAllDashboard()
    {
        Config::set('concrete.seo.url_rewriting', true);
        Config::set('concrete.seo.url_rewriting_all', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', (string) $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page',
                            (string) $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testCanonicalUrl()
    {
        Config::set('concrete.seo.canonical_url', 'http://www.derpco.com');
        \Core::forgetInstance('url/canonical');
        $this->assertEquals('http://www.derpco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.derpco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
        Config::set('concrete.seo.canonical_url', null);
    }

    public function testCanonicalUrlWithPort()
    {
        Config::set('concrete.seo.canonical_url', 'http://www.derpco.com:8080');
        \Core::forgetInstance('url/canonical');
        $this->assertEquals('http://www.derpco.com:8080/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.derpco.com:8080/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
        Config::set('concrete.seo.canonical_url', null);
    }

    public function testURLFunctionWithCanonicalURL()
    {
        Config::set('concrete.seo.canonical_url', 'http://concrete5');
        \Core::forgetInstance('url/canonical');
        $url = URL::to('/dashboard/system/test', 'outstanding');
        $this->assertEquals('http://concrete5/path/to/server/index.php/dashboard/system/test/outstanding', (string) $url);
        Config::set('concrete.seo.canonical_url', null);
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
