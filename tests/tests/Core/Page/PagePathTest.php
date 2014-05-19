<?php

class PagePathTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('PagePaths', 'Pages', 'PageThemes', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
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

    /**
     * Set a canonical page path.
     */
    public function testSettingCanonicalPagePaths()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID'=>1,
            'cName'=> 'My fair page.',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $page->setCanonicalPagePath('/a-completely-new-canonical-page-path');
        $path = $page->getCollectionPathObject();

        $this->assertEquals('/a-completely-new-canonical-page-path', $path->getPagePath());
        $this->assertEquals('my-fair-page', $page->getCollectionHandle());
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

    public function testPagePathUpdate()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID'=>1,
            'cName'=> 'Here\'s a twist',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $nc = $page->getVersionToModify();
        $nc->addAdditionalPagePath('/something/cool', false);
        $nc->addAdditionalPagePath('/something/rad', true);
        $nc->update(array('cName' => 'My new name', 'cHandle' => false));
        $nv = $nc->getVersionObject();
        $nv->approve();

        $nc2 = Page::getByID(2);
        $this->assertEquals('/my-new-name', $nc2->getCollectionPath());
        $this->assertEquals('my-new-name', $nc2->getCollectionHandle());
        $this->assertEquals(2, $nc2->getVersionID());
        $path = $nc2->getCollectionPathObject();

        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $path);
        $this->assertEquals('/my-new-name', $path->getPagePath());
        $this->assertEquals(true, $path->isPagePathCanonical());
        $additionalPaths = $nc2->getAdditionalPagePaths();
        $this->assertEquals(2, count($additionalPaths));
        $this->assertEquals('/something/rad', $additionalPaths[1]->getPagePath());
        $this->assertEquals(false, $additionalPaths[1]->isPagePathCanonical());

    }
}