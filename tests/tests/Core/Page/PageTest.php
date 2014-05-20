<?php

class PageTest extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('Pages', 'PageThemes', 'PagePaths', 'PermissionKeys', 'PermissionKeyCategories', 'PageTypes',
        'PageTemplates', 'Collections', 'CollectionVersions', 'CollectionVersionFeatureAssignments',
        'CollectionAttributeValues', 'CollectionVersionBlockStyles', 'CollectionVersionThemeCustomStyles',
        'CollectionVersionRelatedEdits', 'CollectionVersionAreaStyles', 'CollectionSearchIndexAttributes',
        'PagePermissionAssignments', 'CollectionVersionBlocks', 'Areas', 'PageSearchIndex', 'Config',
        'Logs'); // so brutal

    public function setUp() {
        parent::setUp();
        Page::addHomePage();
        PageTemplate::add('full', 'Full');
        PageType::add(array(
            'handle' => 'basic',
            'name' => 'Basic'
        ));
    }

    public function testBasicCreatePage()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID'=>1,
            'cName'=> 'Test page',
            'pTemplateID' => $template->getPageTemplateID()
        ));
        $this->assertTrue($page instanceof Page);
        $this->assertEquals($page->getCollectionParentID(), 1);
        $this->assertEquals($page->getPageTemplateID(), 1);
        $this->assertEquals($page->getPageTypeID(), 1);
        $this->assertEquals($page->getVersionID(), 1);
    }

    public function testCreatePageFail()
    {
        $badPage = Page::getByID(42069);
        try {
            $page = $badPage->add($ct,array(
                    'uID'=>1,
                    'cName' => 'Stupid Page',
                    'cHandle'=> 'stupid-page'
                ));
        } catch(Exception $e) {
            $caught = true;
        }

        if(!$caught) {
            $this->fail('Added a page to a non-page');
        } else {
            $this->assertTrue(true, 'Successful exception on creating a page beneath a nonexistent parent page.');
        }
    }

    public function testPageMove()
    {
        $page1 = self::createPage('Awesome Page');
        $page2 = self::createPage('Awesome Page 2');

        $page1->move($page2);
        $this->assertEquals($page2->getCollectionParentID(), 1);
        $this->assertEquals($page1->getCollectionParentID(), 3);
        $this->assertEquals($page2->getCollectionPath(), '/awesome-page-2');
        $this->assertEquals($page1->getCollectionPath(), '/awesome-page-2/awesome-page');
    }

    public function testDelete()
    {
        $db = Database::get();
        $page1 = self::createPage('Awesome Page');
        $page2 = self::createPage('Awesome Page 2');
        $this->assertEquals(3, $page2->getCollectionID());
        $page2->delete();

        $np = Page::getByID(3);
        $this->assertEquals($np->getCollectionID(), null);

        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $newpage = $page1->add($pt, array(
            'uID'=>1,
            'cName'=> 'Test Sub-page',
            'pTemplateID' => $template->getPageTemplateID()
        ));

        $page1->delete();
        $this->assertEquals(1, $db->GetOne('select count(cID) from Pages'));

        $np1 = Page::getByID(2);
        $np2 = Page::getByID(4);
        $this->assertEquals($np1->getCollectionID(), null);
        $this->assertEquals($np2->getCollectionID(), null);

    }

    protected static function createPage($name, $parent = false)
    {
        if (!is_object($parent)) {
            $parent = Page::getByID(HOME_CID);
        }

        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $parent->add($pt, array(
            'cName'=> $name,
            'pTemplateID' => $template->getPageTemplateID()
        ));
        return $page;
    }

    /**
     *  @dataProvider pageNames
     */
    public function testPageNames($name, $special)
    {
        $page = self::createPage($name);
        $parentID = $page->getCollectionParentID();
        $this->assertSame($page->getCollectionName(), $name);
        $th = Loader::helper('text');
        if(!$special) {
            $this->assertSame($page->getCollectionPath(), '/'.$th->urlify($name));
            $this->assertSame($page->getCollectionHandle(), $th->urlify($name));
        } else {
            $this->assertSame($page->getCollectionPath(), '/'.(string)$page->getCollectionID());
            $this->assertSame($page->getCollectionHandle(), '');
        }
        $page->delete();
    }

    public function pageNames() {
        return array(
            array('normal page',false),
            array("awesome page's #spring_break98 !!1! SO COOL",false),
            array('niño borracho',false),
            array('雷鶏',true)
        );
    }

    public function testPageDuplicate()
    {
        $page = self::createPage('double vision');
        $home = Page::getByID(HOME_CID);

        $newPage = $page->duplicate($home);
        $realNewPage = Page::getByID($newPage->getCollectionID(),'ACTIVE');

        $this->assertNotEquals($page->getCollectionID(),$realNewPage->getCollectionID());
        $this->assertEquals($page->getCollectionPath().'-2',$realNewPage->getCollectionPath());
        $this->assertEquals($page->getCollectionName().' 2',$realNewPage->getCollectionName());

        $page->delete();
        $realNewPage->delete();
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
        $this->assertEquals('/test-page', $path->getPagePath());
        $this->assertEquals($path->isPagePathCanonical(), true);

        $newpage = $page->add($pt, array(
                'uID'=>1,
                'cName'=> 'Another page for testing!',
                'pTemplateID' => $template->getPageTemplateID()
            ));

        $path = $newpage->getCollectionPathObject();
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
        $testPath = Loader::db()->getEntityManager()->getRepository('\Concrete\Core\Page\PagePath')->findOneBy(
            array('cID' => $page->getCollectionID(), 'ppIsCanonical' => true
        ));
        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $testPath);
        $this->assertEquals('/a-completely-new-canonical-page-path', $testPath->getPagePath());

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

    public function testPagePathSuffixes()
    {
        $about = self::createPage('About');
        $contact = self::createPage('Contact Us', $about);
        $contact2 = self::createPage('Contact Us', $about);

        $this->assertEquals('/about/contact-us-1', $contact2->getCollectionPath());
        $this->assertEquals('/about/contact-us', $contact->getCollectionPath());
        $pathObject = $contact2->getCollectionPathObject();
        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $pathObject);
        $this->assertEquals('/about/contact-us-1', $pathObject->getPagePath());

        $testing1 = self::createPage('Testing');
        $testing2 = self::createPage('Testing', $about);
        $testing1->move($contact);
        $testing2->move($contact);

        $this->assertEquals('/about/contact-us/testing', $testing1->getCollectionPath());
        $this->assertEquals('/about/contact-us/testing-1', $testing2->getCollectionPath());

        $testingPageObject = Page::getByPath('/about/contact-us/testing-1');
        $this->assertEquals(6, $testingPageObject->getCollectionID());
    }

    public function testPagePathEvent()
    {
        $blog = self::createPage('Blog');
        $post1 = self::createPage('Post', $blog);
        $pathObject = $post1->getCollectionPathObject();
        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $pathObject);
        $this->assertEquals('/blog/post', $pathObject->getPagePath());

        Events::addListener('on_compute_canonical_page_path', function($event) {
            $parent = Page::getByID($event->getPageObject()->getCollectionParentID());
            if ($parent->getCollectionPath() == '/blog') {
                // strip off the handle
                $path = substr($event->getPagePath(), 0, strrpos($event->getPagePath(), '/'));
                $path .= '/year/month/day/';
                $path .= $event->getPageObject()->getCollectionHandle();
                $event->setPagePath($path);
            }
        });

        $post2 = self::createPage('Another Post', $blog);
        $this->assertEquals('/blog/year/month/day/another-post', $post2->getCollectionPath());

        $post2Object = Page::getByPath('/blog/year/month/day/another-post');
        $this->assertEquals(4, $post2Object->getCollectionID());

        $addendum = self::createPage('Addendum', $post2Object);
        $path = $addendum->getCollectionPathObject();
        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $path);
        $this->assertEquals('/blog/year/month/day/another-post/addendum', $path->getPagePath());

        $home = Page::getByID(1);
        $addendum->move($home);
        $this->assertEquals('/addendum', $addendum->getCollectionPath());
    }

}