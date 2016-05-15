<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/10/14
 * Time: 7:47 AM.
 */
class PageListTest extends \PageTestCase
{
    /** @var \Concrete\Core\Page\PageList */
    protected $list;

    protected $pageData = array(
        array(
            'Test Page 1', false,
        ),
        array(
            'Abracadabra', false,
        ),
        array(
            'Brace Yourself', false, 'alternate',
        ),
        array(
            'Foobler', '/test-page-1',
        ),
        array(
            'Test Page 2', false,
        ),
        array(
            'Holy Mackerel', false,
        ),
        array(
            'Another Fun Page', false, 'alternate',
        ),
        array(
            'Foo Bar', '/test-page-2',
        ),
        array(
            'Test Page 3', false,
        ),
        array(
            'Another Page', false, 'alternate', 'right_sidebar',
        ),
        array(
            'More Testing', false, 'alternate',
        ),
        array(
            'Foobler', '/another-fun-page', 'another',
        ),
    );

    public function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'PermissionAccessList',
            'PageTypeComposerFormLayoutSets',
            'PermissionAccessEntityTypes',
        ));
        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
            'Concrete\Core\Entity\Page\Feed',
        ));

        parent::setUp();
        \Concrete\Core\Attribute\Key\Category::add('collection');
        \Concrete\Core\Permission\Access\Entity\Type::add('page_owner', 'Page Owner');
        \Concrete\Core\Permission\Category::add('page');
        \Concrete\Core\Permission\Key\Key::add('page', 'view_page', 'View Page', '', 0, 0);
        PageTemplate::add('left_sidebar', 'Left Sidebar');
        PageTemplate::add('right_sidebar', 'Right Sidebar');
        PageType::add(array(
            'handle' => 'alternate',
            'name' => 'Alternate',
        ));
        PageType::add(array(
            'handle' => 'another',
            'name' => 'Another',
        ));

        foreach ($this->pageData as $data) {
            $c = call_user_func_array(array($this, 'createPage'), $data);
            $c->reindex();
        }

        $this->list = new \Concrete\Core\Page\PageList();
        $this->list->ignorePermissions();
    }

    public function testGetUnfilteredTotal()
    {
        $this->assertEquals(13, $this->list->getTotalResults());
    }

    public function testFilterByTypeNone()
    {
        $this->list->filterByPageTypeHandle('fuzzy');
        $this->assertEquals(0, $this->list->getTotalResults());
    }

    public function testFilterByTypeValid1()
    {
        $this->list->filterByPageTypeHandle('basic');
        $this->assertEquals(7, $this->list->getTotalResults());

        $pagination = $this->list->getPagination();
        $this->assertEquals(7, $pagination->getTotalResults());
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals(7, count($results));
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $results[0]);
    }

    public function testFilterByTypeValid2()
    {
        $this->list->filterByPageTypeHandle(array('alternate', 'another'));
        $this->assertEquals(5, $this->list->getTotalResults());
    }

    public function testSortByIDAscending()
    {
        $this->list->sortByCollectionIDAscending();
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals(1, $results[0]->getCollectionID());
        $this->assertEquals(2, $results[1]->getCollectionID());
        $this->assertEquals(3, $results[2]->getCollectionID());
    }

    public function testSortByNameAscending()
    {
        $this->list->sortByName();
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals('Abracadabra', $results[0]->getCollectionName());
        $this->assertEquals('Another Fun Page', $results[1]->getCollectionName());
        $this->assertEquals('Another Page', $results[2]->getCollectionName());
        $this->assertEquals('Brace Yourself', $results[3]->getCollectionName());
    }

    public function testFilterByKeywords()
    {
        $this->list->filterByKeywords('brac', true);
        $total = $this->list->getTotalResults();
        $this->assertEquals(2, $total);
    }

    public function testItemsPerPage()
    {
        $pagination = $this->list->getPagination();
        $pagination->setMaxPerPage(2);
        $pages = $pagination->getCurrentPageResults();
        $this->assertEquals(2, count($pages));
    }

    public function testPaginationObject()
    {
        $this->list->sortByCollectionIDAscending();
        $pagination = $this->list->getPagination();
        $pagination->setMaxPerPage(2);
        $this->assertInstanceOf('\Concrete\Core\Search\Pagination\Pagination', $pagination);
        $this->assertEquals(2, $pagination->getMaxPerPage());
        $this->assertEquals(13, $pagination->getTotalResults());
        $this->assertEquals(1, $pagination->getCurrentPage());
        $this->assertEquals(false, $pagination->hasPreviousPage());
        $this->assertEquals(true, $pagination->hasNextPage());
        $this->assertEquals(true, $pagination->haveToPaginate());
    }

    public function testExcludingAliasesAndBasicGet()
    {
        $subject = Page::getByPath('/test-page-2');
        $parent = Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);
        $this->list->sortBy('cID', 'desc');

        $results = $this->list->getResults();
        $this->assertEquals(13, count($results));
        $this->assertEquals('Foobler', $results[0]->getCollectionName());
    }

    public function testFilterByParentID()
    {
        $subject = Page::getByPath('/test-page-2');
        $parent = Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);
        $parent = Page::getByPath('/another-fun-page');
        $this->list->filterByParentID($parent->getCollectionID());
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals(1, count($results));
        $this->assertEquals(1, $pagination->getTotalResults());
    }

    public function testFilterByPageTypeID()
    {
        $type = \Concrete\Core\Page\Type\Type::getByHandle('alternate');
        $this->list->filterByPageTypeID($type->getPageTypeID());
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        $this->assertEquals(4, count($results));
    }

    public function testFilterByNumChildren()
    {
        $this->list->filterByNumberOfChildren(2, '>=');
        $results = $this->list->getResults();
        $this->assertEquals(1, count($results));
        $this->assertEquals(1, $results[0]->getCollectionID());

        $subject = Page::getByPath('/test-page-2');
        $parent = Page::getByPath('/holy-mackerel');
        $subject->addCollectionAlias($parent);

        $nl = new \Concrete\Core\Page\PageList();
        $nl->ignorePermissions();
        $nl->includeAliases();
        $nl->filterByNumberOfChildren(1, '>=');
        $results = $nl->getTotalResults();
        $this->assertEquals(6, $results);
    }

    public function testFilterByActiveAndSystem()
    {
        \SinglePage::add(Config::get('concrete.paths.trash'));

        $c = Page::getByPath('/test-page-2');
        $c->moveToTrash();

        $results = $this->list->getResults();
        $this->assertEquals(11, count($results));

        $this->list->includeSystemPages(); // This includes the items inside trash because we're stupid.
        $totalResults = $this->list->getTotalResults();
        $this->assertEquals(12, $totalResults);
        $pagination = $this->list->getPagination();
        $this->assertEquals(12, $pagination->getTotalResults());
        $results = $this->list->getResults();
        $this->assertEquals(12, count($results));

        $this->list->includeInactivePages();
        $totalResults = $this->list->getTotalResults();
        $this->assertEquals(14, $totalResults);
        $pagination = $this->list->getPagination();
        $this->assertEquals(14, $pagination->getTotalResults());
        $results = $this->list->getResults();
        $this->assertEquals(14, count($results));
    }

    public function testAliases()
    {
        $parent = Page::getByPath('/test-page-2/foo-bar');
        $subject = Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);

        $pc = Page::getByPath('/brace-yourself');
        $pc->move($parent);

        $page = $this->createPage('Page 2', $parent);
        $page->reindex();

        $this->list->filterByParentID($parent->getCollectionID());
        $this->list->includeAliases();
        $totalResults = $this->list->getTotalResults();
        $this->assertEquals(3, $totalResults);

        $this->list->filterByKeywords('Page');
        $totalResults = $this->list->getTotalResults(); // should get two.
        $this->assertEquals(2, $totalResults);

        $nl = new \Concrete\Core\Page\PageList();
        $nl->includeAliases();
        $nl->ignorePermissions();
        $nl->sortByName();
        $total = $nl->getPagination()->getTotalResults();
        $results = $nl->getPagination()->setMaxPerPage(10)->getCurrentPageResults();
        $this->assertEquals(15, $total);
        $this->assertEquals(10, count($results));
        $this->assertTrue($results[2]->isAlias());
        $this->assertEquals('Another Fun Page', $results[2]->getCollectionName());
        $this->assertEquals($results[2]->getCollectionID(), $subject->getCollectionID());
        $this->assertEquals(14, $results[2]->getCollectionPointerOriginalID());
        $this->assertEquals(8, $results[2]->getCollectionID());
    }

    public function testIndexedSearch()
    {
        $c = Page::getByPath('/another-fun-page');
        $c->update(array('cDescription' => 'A page of all pages.'));
        $c->reindex();

        $this->list->filterByFulltextKeywords('Page');
        $this->list->sortByRelevance();
        $results = $this->list->getResults();
        $this->assertEquals(5, count($results));

        $this->assertEquals(8, $results[0]->getCollectionID());
        $this->assertGreaterThan(0, $results[0]->getPageIndexScore());
        $this->assertGreaterThan(0, $results[1]->getPageIndexScore());
        $this->assertEquals($results[1]->getPageIndexScore(), $results[2]->getPageIndexScore());
    }

    public function testFilterByName()
    {
        $this->list->filterByName('Brace Yourself', true);
        $this->assertEquals(1, $this->list->getTotalResults());

        $nl = new \Concrete\Core\Page\PageList();
        $nl->ignorePermissions();
        $nl->filterByName('Foo', false);
        $this->assertEquals(3, $nl->getTotalResults());
    }

    public function testFilterByPath()
    {
        $this->createPage('More Fun', '/test-page-1/foobler');

        $this->list->filterByPath('/test-page-1');
        $totalResults = $this->list->getTotalResults();
        $this->assertEquals(2, $totalResults);
        $nl = new \Concrete\Core\Page\PageList();
        $nl->ignorePermissions();
        $nl->filterbyPath('/test-page-1', false);
        $pagination = $nl->getPagination();
        $this->assertEquals(1, $pagination->getNBResults());
    }

    public function testBasicFeedSave()
    {
        $pt = \Concrete\Core\Page\Type\Type::getByHandle('another');
        $pp = \Concrete\Core\Page\Page::getByPath('/another-fun-page');
        $pf = new \Concrete\Core\Entity\Page\Feed();
        $pf->setHandle('blog');
        $pf->setPageTypeID($pt->getPageTypeID());
        $pf->setParentID($pp->getCollectionID());
        $pf->setTitle('RSS Feed');
        $pf->setDescription('My Description');
        $pf->save();

        $this->assertEquals('blog', $pf->getHandle());
        $this->assertEquals(1, $pf->getID());

        $pf->ignorePermissions();
        $pl = $pf->getPageListObject();
        $this->assertInstanceOf('\Concrete\Core\Page\PageList', $pl);
        $this->assertEquals(1, $pl->getTotalResults());

        $results = $pl->getResults();
        $this->assertEquals('Foobler', $results[0]->getCollectionName());
    }
}
