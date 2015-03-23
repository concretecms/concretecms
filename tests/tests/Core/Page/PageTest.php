<?php

class PageTest extends PageTestCase {

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

    public function testSinglePagesPath()
    {
        SinglePage::add('/dashboard/reports');
        SinglePage::add('/dashboard/system/attributes');
        $reportsPage = Page::getByPath('/dashboard/reports');
        $attrPage = Page::getByPath('/dashboard/system/attributes');
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $reportsPage);
        $this->assertTrue($reportsPage->getCollectionID() > 0);
        $this->assertEquals('/dashboard/reports.php', $reportsPage->getCollectionFilename());
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $attrPage);
        $this->assertTrue($attrPage->getCollectionID() > 0);
        $this->assertEquals('/dashboard/system/attributes/view.php', $attrPage->getCollectionFilename());
    }

    public function testSinglePagesController() {
        $reportsPage = SinglePage::add('/dashboard/reports/surveys');
        $reportsPage = Page::getByPath('/dashboard/reports/surveys');
        $controller = $reportsPage->getController();
        $this->assertInstanceOf('\Concrete\Controller\SinglePage\Dashboard\Reports\Surveys', $controller);
    }

    public function testSystemPageBoolean()
    {
        SinglePage::add('/dashboard/reports');
        $reportsPage = Page::getByPath('/dashboard/reports');
        $this->assertEquals(true, $reportsPage->isSystemPage());

        $page2 = self::createPage('Awesome Page 2');
        $this->assertEquals(false, $page2->isSystemPage());

        $account = SinglePage::add('/account');
        SinglePage::add('/account/profile');
        $profile = Page::getByPath('/account/profile');
        $this->assertEquals(true, $account->isSystemPage());
        $this->assertEquals(true, $profile->isSystemPage());
        $page2->move($profile);
        $this->assertEquals(true, $page2->isSystemPage());
        $page2 = Page::getByPath('/account/profile/awesome-page-2');
        $this->assertEquals(true, $page2->isSystemPage());
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

    public function testPageDuplications()
    {
        $page1 = self::createPage('Page 1');
        $page2 = self::createPage('Page 2');
        $page3 = self::createPage('Page 3');
        $page4 = self::createPage('Page 4');

        $subpageA = self::createPage('Subpage A', $page2);
        self::createPage('Subpage B', $page2);
        self::createPage('Subpage C', $page2);

        $page1->duplicate($subpageA);
        $page2->duplicateAll($page4);
        $page3->duplicate($page1);


        // it's a little lame that we have to re-get the objects
        // in order for them to be in sync but fixing this is outside of what I want to do right now.
        $page1 = Page::getByPath('/page-1');
        $page2 = Page::getByPath('/page-2');
        $page4 = Page::getByPath('/page-4');
        $this->assertEquals(1, $page1->getNumChildren());
        $this->assertEquals(3, $page2->getNumChildren());
        $this->assertEquals(1, $page4->getNumChildren()); // direct children.

        $page = Page::getByPath('/page-4/page-2/subpage/page-1');
        $this->assertFalse($page->isError());
        $pagePath = $page->getCollectionPathObject();
        $this->assertInstanceOf('\Concrete\Core\Page\PagePath', $pagePath);
        $this->assertEquals('/page-4/page-2/subpage/page-1', $pagePath->getPagePath());
        $this->assertTrue($pagePath->isPagePathCanonical());
    }

    public function testCustomCanonicalURLs()
    {
        $cache = Core::make('cache/request');
        $cache->disable();

        $about = self::createPage('About');
        $contact = self::createPage('Contact Us', $about);

        $c = Page::getByID(3);
        $this->assertEquals('Contact Us', $c->getCollectionName());
        $this->assertEquals('/about/contact-us', $c->getCollectionPath());

        $p = new \Concrete\Core\Page\PagePath();
        $p->setPagePath('/contact');
        $p->setPageObject($contact);
        $p->setPagePathIsCanonical(true);

        $c->clearPagePaths();

        $db = Loader::db();
        $db->getEntityManager()->persist($p);
        $db->getEntityManager()->flush();

        $c = Page::getByID(3);
        $this->assertEquals('/contact', $c->getCollectionPath());

        $c->rescanCollectionPath();

        $c = Page::getByID(3);
        $this->assertEquals('/contact', $c->getCollectionPath());

    }


}