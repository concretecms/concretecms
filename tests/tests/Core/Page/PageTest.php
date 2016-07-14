<?php

class PageTest extends PageTestCase
{

    public function testHomePageExists()
    {
        $home = Page::getByID(HOME_CID);
        $this->assertTrue($home instanceof Page);
        $this->assertEquals(0, $home->getCollectionParentID());
        $this->assertEquals(1, $home->getCollectionID());
    }

    public function testBasicCreatePage()
    {
        $home = Page::getByID(HOME_CID);
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, array(
            'uID' => 1,
            'cName' => 'Test page',
            'pTemplateID' => $template->getPageTemplateID(),
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
            $page = $badPage->add($ct, array(
                    'uID' => 1,
                    'cName' => 'Stupid Page',
                    'cHandle' => 'stupid-page',
                ));
        } catch (Exception $e) {
            $caught = true;
        }

        if (!$caught) {
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

    public function testSinglePagesController()
    {
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
            'uID' => 1,
            'cName' => 'Test Sub-page',
            'pTemplateID' => $template->getPageTemplateID(),
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
        if (!$special) {
            $this->assertSame($page->getCollectionPath(), '/'.$th->urlify($name));
            $this->assertSame($page->getCollectionHandle(), $th->urlify($name));
        } else {
            $this->assertSame($page->getCollectionPath(), '/'.(string) $page->getCollectionID());
            $this->assertSame($page->getCollectionHandle(), '');
        }
        $page->delete();
    }

    public function pageNames()
    {
        return array(
            array('normal page', false),
            array("awesome page's #spring_break98 !!1! SO COOL", false),
            array('niño borracho', false),
            array('雷鶏', true),
        );
    }

    public function testPageDuplicate()
    {
        $page = self::createPage('double vision');
        $home = Page::getByID(HOME_CID);

        $newPage = $page->duplicate($home);
        $realNewPage = Page::getByID($newPage->getCollectionID(), 'ACTIVE');

        $this->assertNotEquals($page->getCollectionID(), $realNewPage->getCollectionID());
        $this->assertEquals($page->getCollectionPath().'-2', $realNewPage->getCollectionPath());
        $this->assertEquals($page->getCollectionName().' 2', $realNewPage->getCollectionName());

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
        $this->assertInstanceOf('\Concrete\Core\Entity\Page\PagePath', $pagePath);
        $this->assertEquals('/page-4/page-2/subpage/page-1', $pagePath->getPagePath());
        $this->assertTrue($pagePath->isPagePathCanonical());
    }

    protected function setupAliases()
    {
        $about = self::createPage('About');
        $search = self::createPage('Search');
        $contact = self::createPage('Contact Us', $about);
        $another = self::createPage('Another Page', $about);
        $awesome = self::createPage('Awesome');

        return array('awesome' => $awesome, 'about' => $about, 'search' => $search, 'contact' => $contact, 'another' => $another);
    }

    public function testPageAlias()
    {
        extract($this->setupAliases());

        $cID = $search->addCollectionAlias($another);

        $this->assertEquals(2, $about->getCollectionID());
        $this->assertEquals(3, $search->getCollectionID());
        $this->assertEquals(4, $contact->getCollectionID());
        $this->assertEquals(5, $another->getCollectionID());
        $this->assertEquals(6, $awesome->getCollectionID());
        $this->assertEquals(7, $cID);

        $alias = Page::getByID($cID);
        $this->assertEquals(3, $alias->getCollectionID());
        $this->assertEquals(3, $alias->getCollectionPointerID());
        $this->assertTrue($alias->isAlias());
        $this->assertEquals(7, $alias->getCollectionPointerOriginalID());
        $this->assertEquals('/about/another-page/search', $alias->getCollectionPath());
    }

    public function testPageAliasDirectDelete()
    {
        extract($this->setupAliases());
        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);

        $alias->delete();
        $search = Page::getByID(3);
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $search);
        $this->assertEquals(3, $search->getCollectionID());
        $this->assertFalse($search->isError());

        $alias = Page::getByID(7);
        $this->assertEquals(COLLECTION_NOT_FOUND, $alias->getError());
    }

    public function testPageAliasPageMove()
    {
        extract($this->setupAliases());
        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);
        $about->move($awesome);

        $about = Page::getByID(2);
        $search = Page::getByID(3);
        $contact = Page::getByID(4);
        $another = Page::getByID(5);
        $awesome = Page::getByID(6);
        $alias = Page::getByID(7);

        $this->assertTrue($alias->isAlias());

        $this->assertEquals('/awesome/about', $about->getCollectionPath());
        $this->assertEquals('/awesome/about/another-page/search', $alias->getCollectionPath());
        $this->assertEquals('/search', $search->getCollectionPath());
        $this->assertEquals('/awesome/about/contact-us', $contact->getCollectionPath());
        $this->assertEquals('/awesome/about/another-page', $another->getCollectionPath());
        $this->assertEquals('/awesome', $awesome->getCollectionPath());
        $this->assertEquals(3, $alias->getCollectionID());
        $this->assertEquals(7, $alias->getCollectionPointerOriginalID());
    }

    public function testPageAliasParentDelete()
    {
        extract($this->setupAliases());
        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);

        $about->delete();
        $about = Page::getByID(2);
        $search = Page::getByID(3);
        $contact = Page::getByID(4);
        $another = Page::getByID(5);
        $awesome = Page::getByID(6);
        $alias = Page::getByID(7);

        $this->assertEquals(COLLECTION_NOT_FOUND, $about->getError());
        $this->assertEquals(3, $search->getCollectionID());
        $this->assertEquals(false, $search->isError());
        $this->assertEquals(COLLECTION_NOT_FOUND, $contact->getError());
        $this->assertEquals(COLLECTION_NOT_FOUND, $another->getError());
        $this->assertEquals(6, $awesome->getCollectionID());
        $this->assertEquals(false, $awesome->isError());

        $this->assertEquals(COLLECTION_NOT_FOUND, $alias->getError());
    }

    public function testPageMoveToTrashNoAliases()
    {
        \SinglePage::add(Config::get('concrete.paths.trash'));

        $this->setupAliases();

        $about = Page::getByID(3);
        $about->moveToTrash();

        // note –all the hard coded numerical IDs are increased by one here because we have added
        // the trash node.
        $about = Page::getByID(3);
        $search = Page::getByID(4);
        $contact = Page::getByID(5);
        $another = Page::getByID(6);
        $awesome = Page::getByID(7);

        $this->assertFalse($about->isActive());
        $this->assertFalse($contact->isActive());
        $this->assertFalse($another->isActive());
        $this->assertTrue($search->isActive());
        $this->assertTrue($awesome->isActive());

        $this->assertTrue($about->isInTrash());
        $this->assertTrue($contact->isInTrash());
        $this->assertTrue($another->isInTrash());
        $this->assertFalse($search->isInTrash());
        $this->assertFalse($awesome->isInTrash());

        $this->assertEquals('/search', $search->getCollectionPath());
    }

    public function testPageMoveToTrashAliases()
    {
        \SinglePage::add(Config::get('concrete.paths.trash'));

        extract($this->setupAliases());

        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);

        $this->assertEquals(8, $cID);
        $this->assertTrue($alias->isAlias());

        $about = Page::getByID(3);
        $about->moveToTrash();

        $about = Page::getByID(3);
        $search = Page::getByID(4);
        $another = Page::getByID(6);
        $awesome = Page::getByID(7);
        $searchAlias = Page::getByID(8);

        $this->assertFalse($about->isActive());
        $this->assertFalse($another->isActive());
        $this->assertFalse($searchAlias->isActive());
        $this->assertTrue($awesome->isActive());
        $this->assertTrue($search->isActive());

        $this->assertEquals('/!trash/about', $about->getCollectionPath());
        $this->assertEquals('/!trash/about/another-page', $another->getCollectionPath());
        $this->assertEquals('/!trash/about/another-page/search', $searchAlias->getCollectionPath());
        $this->assertEquals('/awesome', $awesome->getCollectionPath());
        $this->assertEquals('/search', $search->getCollectionPath());

        $this->assertTrue($about->isInTrash());
        $this->assertTrue($another->isInTrash());
        $this->assertTrue($searchAlias->isInTrash());
        $this->assertFalse($awesome->isInTrash());
        $this->assertFalse($search->isInTrash());
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

        $p = new \Concrete\Core\Entity\Page\PagePath();
        $p->setPagePath('/contact');
        $p->setPageObject($contact);
        $p->setPagePathIsCanonical(true);

        $c->clearPagePaths();

        $em = \ORM::entityManager('core');
        $em->persist($p);
        $em->flush();

        $c = Page::getByID(3);
        $this->assertEquals('/contact', $c->getCollectionPath());

        $c->rescanCollectionPath();

        $c = Page::getByID(3);
        $this->assertEquals('/contact', $c->getCollectionPath());
    }
}
