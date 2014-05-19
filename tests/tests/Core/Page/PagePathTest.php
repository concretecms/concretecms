<?php

class PagePathTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('PagePaths', 'Pages', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
        'PageTemplates', 'Collections', 'CollectionVersions'); // so brutal

    public function setUp() {
        parent::setUp();
        Page::addHomePage();
        PageTemplate::add('full', 'Full');
        PageType::add(array(
            'handle' => 'basic',
            'name' => 'Basic'
        ));
    }

    /**
     * Add a page and check its canonical path.
     */
    public function testCanonicalPagePaths()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID'=>1,
            'cName'=> 'Test page',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $path = $page->getCollectionPathObject();
        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $path);
        $this->assertEquals($path->getPagePathID(), 1);
        $this->assertEquals($path->getPagePath(), '/test-page');
        $this->assertEquals($path->isPagePathCanonical(), true);

        $newpage = $page->add($pt, array(
            'uID'=>1,
            'cName'=> 'Another page for testing!',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $path = $newpage->getCollectionPathObject();
        $this->assertEquals($path->getPagePathID(), 2);
        $this->assertEquals($path->getPagePath(), '/test-page/another-page-for-testing');
        $this->assertEquals($path->isPagePathCanonical(), true);
    }

    public function testNonCanonicalPagePaths()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID'=>1,
            'cName'=> 'About',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $path1 = $page->addAdditionalPagePath('/about-us');
        $path2 = $page->addAdditionalPagePath('/another/path/to/the/about/page');

        $this->assertEquals($path1->getPagePath(), '/about-us');
        $canonicalpath = $page->getCollectionPathObject();
        $this->assertEquals($canonicalpath->getCollectionID(), 2);
        $this->assertEquals($canonicalpath->getPagePath(), '/about');
        $this->assertEquals($path2->getPagePath(), '/another/path/to/the/about/page');
        $this->assertEquals($path2->isPagePathCanonical(), false);

        $pathArray = $page->getAdditionalPagePaths();
        $this->assertEquals(2, count($pathArray));
        $this->assertEquals($pathArray[1], $path2);

        $page->clearAdditionalPagePaths();
        $pathArray = $page->getAdditionalPagePaths();
        $this->assertEquals(0, count($pathArray));
    }

}