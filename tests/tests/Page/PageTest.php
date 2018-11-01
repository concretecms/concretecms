<?php

namespace Concrete\Tests\Page;

use Concrete\TestHelpers\Page\PageTestCase;
use Config;
use Core;
use Database;
use Exception;
use Loader;
use Page;
use PageTemplate;
use PageType;
use SinglePage;

class PageTest extends PageTestCase
{
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testHomePageExists()
    {
        $home = Page::getByID(Page::getHomePageID());
        $this->assertTrue($home instanceof Page);
        $this->assertEquals(0, $home->getCollectionParentID());
        $this->assertEquals(1, $home->getCollectionID());
    }

    public function testBasicCreatePage()
    {
        $home = Page::getByID(Page::getHomePageID());
        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);
        $page = $home->add($pt, [
            'uID' => 1,
            'cName' => 'Test page',
            'pTemplateID' => $template->getPageTemplateID(),
        ]);
        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals($page->getCollectionParentID(), 1);
        $this->assertEquals($page->getPageTemplateID(), 1);
        $this->assertEquals($page->getPageTypeID(), 1);
        $this->assertEquals($page->getVersionID(), 1);

        $page->delete();
    }

    public function testCreatePageFail()
    {
        $badPage = Page::getByID(42069);
        try {
            $page = $badPage->add(null, [
                'uID' => 1,
                'cName' => 'Stupid Page',
                'cHandle' => 'stupid-page',
            ]);
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
        $this->assertEquals($page2->getCollectionParentID(), Page::getHomePageID());
        $this->assertEquals($page1->getCollectionParentID(), $page2->getCollectionID());
        $this->assertEquals($page2->getCollectionPath(), '/awesome-page-2');
        $this->assertEquals($page1->getCollectionPath(), '/awesome-page-2/awesome-page');

        $page1->delete();
        $page2->delete();
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

        Page::getByPath('/dashboard')->delete();
    }

    public function testSinglePagesController()
    {
        $reportsPage = SinglePage::add('/dashboard/reports/surveys');
        $reportsPage = Page::getByPath('/dashboard/reports/surveys');
        $controller = $reportsPage->getController();
        $this->assertInstanceOf('\Concrete\Controller\SinglePage\Dashboard\Reports\Surveys', $controller);

        $reportsPage->delete();
    }

    public function testSystemPageBoolean()
    {
        if (($page = Page::getByPath('/dashboard')) && !$page->isError()) {
            $page->delete();
        }

        SinglePage::addGlobal('/dashboard/reports');
        $reportsPage = Page::getByPath('/dashboard/reports');
        $this->assertEquals(true, $reportsPage->isSystemPage());

        $page2 = self::createPage('Awesome Page 2');
        $this->assertEquals(false, $page2->isSystemPage());

        $account = SinglePage::addGlobal('/account');
        SinglePage::addGlobal('/account/profile');
        $profile = Page::getByPath('/account/profile');
        $this->assertEquals(true, $account->isSystemPage());
        $this->assertEquals(true, $profile->isSystemPage());
        $page2->move($profile);
        $this->assertEquals(true, $page2->isSystemPage());
        $page2 = Page::getByPath('/account/profile/awesome-page-2');
        $this->assertEquals(true, $page2->isSystemPage());

        $account->delete();
        $reportsPage->delete();
        $page2->delete();
    }

    public function testDelete()
    {
        $db = Database::get();

        $page2 = self::createPage('Awesome Page 2');
        $page2->delete();

        $np = Page::getByID($page2->getCollectionID());
        $this->assertEquals($np->getCollectionID(), null);

        $pt = PageType::getByID(1);
        $template = PageTemplate::getByID(1);

        $page1 = self::createPage('Awesome Page');
        $newpage = $page1->add($pt, [
            'uID' => 1,
            'cName' => 'Test Sub-page',
            'pTemplateID' => $template->getPageTemplateID(),
        ]);

        $page1->delete();
        $np1 = Page::getByID($page1->getCollectionID());
        $np2 = Page::getByID($page2->getCollectionID());
        $this->assertEquals($np1->getCollectionID(), null);
        $this->assertEquals($np2->getCollectionID(), null);
    }

    /**
     * @dataProvider pageNames
     *
     * @param mixed $name
     * @param mixed $special
     */
    public function testPageNames($name, $special)
    {
        $page = self::createPage($name);
        $parentID = $page->getCollectionParentID();
        $this->assertSame($page->getCollectionName(), $name);
        $th = Loader::helper('text');
        if (!$special) {
            $this->assertSame($page->getCollectionPath(), '/' . $th->urlify($name));
            $this->assertSame($page->getCollectionHandle(), $th->urlify($name));
        } else {
            $this->assertSame($page->getCollectionPath(), '/' . (string) $page->getCollectionID());
            $this->assertSame($page->getCollectionHandle(), '');
        }
        $page->delete();
    }

    public function pageNames()
    {
        return [
            ['normal page', false],
            ["awesome page's #spring_break98 !!1! SO COOL", false],
            ['niño borracho', false],
            ['雷鶏', true],
        ];
    }

    public function testPageDuplicate()
    {
        $page = self::createPage('double vision');
        $home = Page::getByID(Page::getHomePageID());

        $newPage = $page->duplicate($home);
        $realNewPage = Page::getByID($newPage->getCollectionID(), 'ACTIVE');

        $this->assertNotEquals($page->getCollectionID(), $realNewPage->getCollectionID());
        $this->assertEquals($page->getCollectionPath() . '-2', $realNewPage->getCollectionPath());
        $this->assertEquals($page->getCollectionName() . ' 2', $realNewPage->getCollectionName());

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

        $page1->delete();
        $page2->delete();
        $page3->delete();
        $page4->delete();
    }

    public function testPageAlias()
    {
        $aliases = $this->setupAliases();
        extract($aliases);

        $cID = $search->addCollectionAlias($another);

        $this->assertNotNull($about->getCollectionID());
        $this->assertNotNull($search->getCollectionID());
        $this->assertNotNull($contact->getCollectionID());
        $this->assertNotNull($another->getCollectionID());
        $this->assertNotNull($awesome->getCollectionID());
        $this->assertNotNull($cID);

        $alias = Page::getByID($cID);
        $this->assertEquals($search->getCollectionID(), $alias->getCollectionID());
        $this->assertEquals($search->getCollectionID(), $alias->getCollectionPointerID());
        $this->assertTrue($alias->isAlias());
        $this->assertEquals($cID, $alias->getCollectionPointerOriginalID());
        $this->assertEquals('/about/another-page/search', $alias->getCollectionPath());

        foreach ($aliases as $alias) {
            $alias->delete();
        }
    }

    public function testPageAliasDirectDelete()
    {
        extract($aliases = $this->setupAliases());
        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);

        $alias->delete();
        $search = Page::getByID($search->getCollectionID());
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $search);
        $this->assertNotNull($search->getCollectionID());
        $this->assertFalse($search->isError());

        $alias = Page::getByID($cID);
        $this->assertNull($alias->getCollectionID());
        $this->assertEquals(COLLECTION_NOT_FOUND, $alias->getError());

        foreach ($aliases as $alias) {
            $alias->delete();
        }
    }

    public function testPageAliasPageMove()
    {
        extract($aliases = $this->setupAliases());
        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);
        $about->move($awesome);

        $about = Page::getByID($about->getCollectionID());
        $search = Page::getByID($search->getCollectionID());
        $contact = Page::getByID($contact->getCollectionID());
        $another = Page::getByID($another->getCollectionID());
        $awesome = Page::getByID($awesome->getCollectionID());
        $alias = Page::getByID($cID);

        $this->assertTrue($alias->isAlias());

        $this->assertEquals('/awesome/about', $about->getCollectionPath());
        $this->assertEquals('/awesome/about/another-page/search', $alias->getCollectionPath());
        $this->assertEquals('/search', $search->getCollectionPath());
        $this->assertEquals('/awesome/about/contact-us', $contact->getCollectionPath());
        $this->assertEquals('/awesome/about/another-page', $another->getCollectionPath());
        $this->assertEquals('/awesome', $awesome->getCollectionPath());
        $this->assertNotEmpty($alias->getCollectionID());
        $this->assertEquals($cID, $alias->getCollectionPointerOriginalID());

        foreach ($aliases as $alias) {
            $alias->delete();
        }
    }

    public function testPageAliasParentDelete()
    {
        extract($aliases = $this->setupAliases());
        $cID = $about->addCollectionAlias($another);
        $alias = Page::getByID($cID);

        $about->delete();
        $about = Page::getByID($about->getCollectionID());
        $search = Page::getByID($search->getCollectionID());
        $contact = Page::getByID($contact->getCollectionID());
        $another = Page::getByID($another->getCollectionID());
        $awesome = Page::getByID($awesome->getCollectionID());
        $alias = Page::getByID($cID);

        $this->assertEquals(COLLECTION_NOT_FOUND, $about->getError());
        $this->assertGreaterThan(0, $search->getCollectionID());
        $this->assertEquals(false, $search->isError());
        $this->assertEquals(COLLECTION_NOT_FOUND, $contact->getError());
        $this->assertEquals(COLLECTION_NOT_FOUND, $another->getError());
        $this->assertGreaterThan(0, $awesome->getCollectionID());
        $this->assertEquals(false, $awesome->isError());

        foreach ($aliases as $alias) {
            $alias->delete();
        }
    }

    public function testPageMoveToTrashNoAliases()
    {
        \SinglePage::add(Config::get('concrete.paths.trash'));

        extract($aliases = $this->setupAliases());

        $about->moveToTrash();

        $about = Page::getByID($about->getCollectionID());
        $search = Page::getByID($search->getCollectionID());
        $contact = Page::getByID($contact->getCollectionID());
        $another = Page::getByID($another->getCollectionID());
        $awesome = Page::getByID($awesome->getCollectionID());

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

        foreach ($aliases as $alias) {
            $alias->delete();
        }
    }

    public function testPageMoveToTrashAliases()
    {
        \SinglePage::add(Config::get('concrete.paths.trash'));

        extract($aliases = $this->setupAliases());

        $cID = $search->addCollectionAlias($another);
        $alias = Page::getByID($cID);
        $this->assertTrue($alias->isAlias());

        $about->moveToTrash();

        $about = Page::getByID($about->getCollectionID());
        $search = Page::getByID($search->getCollectionID());
        $another = Page::getByID($another->getCollectionID());
        $awesome = Page::getByID($awesome->getCollectionID());
        $searchAlias = Page::getByID($cID);

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

        foreach ($aliases as $alias) {
            $alias->delete();
        }
    }

    public function testCustomCanonicalURLs()
    {
        $cache = Core::make('cache/request');
        $cache->disable();

        $aliases = $this->setupAliases();
        $about = $aliases['about'];
        $contact = $aliases['contact'];

        $contact = Page::getByID($contact->getCollectionID());
        $this->assertEquals('Contact Us', $contact->getCollectionName());
        $this->assertEquals('/about/contact-us', $contact->getCollectionPath());

        $p = new \Concrete\Core\Entity\Page\PagePath();
        $p->setPagePath('/contact');
        $p->setPageObject($contact);
        $p->setPagePathIsCanonical(true);

        $contact->clearPagePaths();

        $em = \ORM::entityManager('core');
        $em->persist($p);
        $em->flush();

        $contact = Page::getByID($contact->getCollectionID());
        $this->assertEquals('/contact', $contact->getCollectionPath());

        $contact->rescanCollectionPath();

        $contact = Page::getByID($contact->getCollectionID());
        $this->assertEquals('/contact', $contact->getCollectionPath());

        $about->delete();
        $contact->delete();
    }

    public function testPageUpdate()
    {
        /** @var Concrete\Core\Page\Page $page */
        $page = self::createPage('Awesome Page');
        $nvc = $page->getVersionToModify();
        $data = [
            'cName' => 'Amazing Page',
            'cDescription' => 'This is amazing page.',
            'cDatePublic' => '2017-05-11 10:05:00',
            'cHandle' => 'amazing-page',
        ];
        $nvc->update($data);
        $v = \Concrete\Core\Page\Collection\Version\Version::get($page, 'RECENT');
        $v->approve();

        $page = Page::getByID($page->getCollectionID());
        $this->assertEquals($data['cName'], $page->getCollectionName());
        $this->assertEquals($data['cDescription'], $page->getCollectionDescription());
        $this->assertEquals($data['cDatePublic'], $page->getCollectionDatePublic());
        $this->assertEquals($data['cHandle'], $page->getCollectionHandle());
        $this->assertEquals('/amazing-page', $page->getCollectionPath());

        $page->delete();
    }

    /**
     * Test if the on_page_display_order_update event works OK.
     */
    public function testPageDisplayOrderUpdateFiresEvent()
    {
        $parent = self::createPage('Parent page to test display order of sub pages');
        $page1 = self::createPage('Page1', $parent);
        $page2 = self::createPage('Page2', $parent);
        $page3 = self::createPage('Page3', $parent);

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $director */
        $director = $this->app->make('director');

        $map = [];
        $listener = function ($event) use (&$map) {
            $map[$event->getPageObject()->getCollectionName()]['old'] = $event->getOldDisplayOrder();
            $map[$event->getPageObject()->getCollectionName()]['new'] = $event->getNewDisplayOrder();
        };

        $director->addListener('on_page_display_order_update', $listener);

        $page1->updateDisplayOrder(10);
        $this->assertEquals(0, $map[$page1->getCollectionName()]['old'], 'First page should always be index 0.');
        $this->assertEquals(10, $map[$page1->getCollectionName()]['new']);

        $page2->updateDisplayOrder(5);
        $this->assertEquals(1, $map[$page2->getCollectionName()]['old'], 'Second page should always be index 1.');
        $this->assertEquals(5, $map[$page2->getCollectionName()]['new']);

        $page1->updateDisplayOrder(99, $page3->getCollectionID());
        $this->assertEquals(2, $map[$page3->getCollectionName()]['old'], 'Third page should always be index 2.');
        $this->assertEquals(99, $map[$page3->getCollectionName()]['new']);

        $director->removeListener('on_page_display_order_update', $listener);
    }

    /**
     * @return \Concrete\Core\Page\Page[]
     */
    protected function setupAliases()
    {
        if (($about = Page::getByPath('/about')) || !$about->getCollectionID()) {
            $about = self::createPage('About');
        }
        if ((!$search = Page::getByPath('/search')) || !$search->getCollectionID()) {
            $search = self::createPage('Search');
        }
        if ((!$contact = Page::getByPath('/about/contact-us')) || !$contact->getCollectionID()) {
            $contact = self::createPage('Contact Us', $about);
        }
        if ((!$another = Page::getByPath('/about/another-page')) || !$another->getCollectionID()) {
            $another = self::createPage('Another Page', $about);
        }
        if ((!$awesome = Page::getByPath('/awesome')) || !$awesome->getCollectionID()) {
            $awesome = self::createPage('Awesome');
        }

        return [
            'awesome' => $awesome,
            'about' => $about,
            'search' => $search,
            'contact' => $contact,
            'another' => $another,
        ];
    }
}
