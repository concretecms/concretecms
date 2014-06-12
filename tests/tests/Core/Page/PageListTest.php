<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/10/14
 * Time: 7:47 AM
 */

class PageListTest extends \PageTestCase {

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
            'Brace Yourself', false, 'alternate'
        ),
        array(
            'Foobler', '/test-page-1',
        ),
        array(
            'Test Page 2', false
        ),
        array(
            'Holy Mackerel', false
        ),
        array(
            'Another Fun Page', false, 'alternate'
        ),
        array(
            'Foo Bar', '/test-page-2'
        ),
        array(
            'Test Page 3', false
        ),
        array(
            'Another Page', false, 'alternate', 'right_sidebar'
        ),
        array(
            'More Testing', false, 'alternate'
        ),
        array(
            'Foobler', '/another-fun-page'
        )
    );

    public function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'PermissionAccessList',
            'PageTypeComposerFormLayoutSets',
            'AttributeSetKeys',
            'AttributeSets',
            'AttributeKeyCategories',
            'PermissionAccessEntityTypes',
            'Packages',
            'AttributeKeys',
            'AttributeTypes'

        ));

        parent::setUp();
        \Concrete\Core\Permission\Access\Entity\Type::add('page_owner', 'Page Owner');
        \Concrete\Core\Permission\Category::add('page');
        \Concrete\Core\Permission\Key\Key::add('page', 'view_page', 'View Page', '', 0, 0);
        PageTemplate::add('left_sidebar', 'Left Sidebar');
        PageTemplate::add('right_sidebar', 'Right Sidebar');
        PageType::add(array(
            'handle' => 'alternate',
            'name' => 'Alternate'
        ));

        foreach($this->pageData as $data) {
            $c = call_user_func_array(array($this, 'createPage'), $data);
            $c->reindex();
        }

        $this->list = new \Concrete\Core\Page\PageList();
        $this->list->ignorePermissions();
    }

    protected function addAlias()
    {
        $subject = Page::getByPath('/test-page-2');
        $parent = Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);
    }

    public function testGetUnfilteredTotal()
    {
        $this->assertEquals(13, $this->list->getTotal());
    }

    public function testFilterByTypeNone()
    {
        $this->list->filterByPageTypeHandle('fuzzy');
        $this->assertEquals(0, $this->list->getTotal());
    }

    public function testFilterByTypeValid1()
    {
        $this->list->filterByPageTypeHandle('basic');
        $this->assertEquals(8, $this->list->getTotal());
        $results = $this->list->getPage();
        $this->assertEquals(8, count($results));
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $results[0]);
    }

    public function testFilterByTypeValid2()
    {
        $this->list->filterByPageTypeHandle('alternate');
        $this->assertEquals(4, $this->list->getTotal());
    }

    public function testSortByIDAscending()
    {
        $this->list->sortByCollectionIDAscending();
        $results = $this->list->getPage();
        $this->assertEquals(1, $results[0]->getCollectionID());
        $this->assertEquals(2, $results[1]->getCollectionID());
        $this->assertEquals(3, $results[2]->getCollectionID());
    }

    public function testSortByNameAscending()
    {
        $this->list->sortByName();
        $results = $this->list->getPage();
        $this->assertEquals('Abracadabra', $results[0]->getCollectionName());
        $this->assertEquals('Another Fun Page', $results[1]->getCollectionName());
        $this->assertEquals('Another Page', $results[2]->getCollectionName());
        $this->assertEquals('Brace Yourself', $results[3]->getCollectionName());
    }

    public function testFilterByKeywords()
    {
        $this->list->filterByKeywords('brac', true);
        $total = $this->list->getTotal();
        $this->assertEquals(2, $total);
    }

    public function testItemsPerPage()
    {
        $this->list->setItemsPerPage(2);
        $pages = $this->list->getPage();
        $this->assertEquals(2, count($pages));
    }

    public function testSummary()
    {
        $this->list->setItemsPerPage(2);
        $this->list->sortByCollectionIDAscending();
        $summary = $this->list->getSummary();
        $this->assertInstanceOf('stdClass', $summary);
        $this->assertEquals(2, $summary->chunk);
        $this->assertEquals('asc', $summary->order);
        $this->assertEquals(0, $summary->startAt);
        $this->assertEquals(13, $summary->total);
        $this->assertEquals(1, $summary->current);
        $this->assertEquals(-1, $summary->previous);
        $this->assertEquals(2, $summary->next);
        $this->assertEquals(12, $summary->last);
        $this->assertEquals(1, $summary->currentStart);
        $this->assertEquals(2, $summary->currentEnd);
        $this->assertEquals(true, $summary->needsPaging);
    }

    public function testAliasingAndBasicGet()
    {
        $this->addAlias();
        $this->list->sortBy('cID', 'desc');

        $results = $this->list->get();
        $this->assertEquals(14, count($results));
        $this->assertEquals('Test Page 2', $results[0]->getCollectionName());
        $this->assertEquals(true, $results[0]->isAlias());
    }

    public function testFilterByParentID()
    {
        $this->addAlias();
        $parent = Page::getByPath('/another-fun-page');
        $this->list->filterByParentID($parent->getCollectionID());
        $results = $this->list->getPage();
        $this->assertEquals(2, count($results));
        $this->assertEquals(2, $this->list->getTotal());
    }

    public function testFilterByApproved()
    {
        $this->addAlias();
        $parent = Page::getByPath('/test-page-1/foobler');
        $type = PageType::getByHandle('basic');
        $template = PageTemplate::getByHandle('left_sidebar');
        $c = $parent->add($type, array(
            'cName' => 'This is an unapproved page.',
            'pTemplateID' => $template->getPageTemplateID(),
            'cvIsApproved' => false
        ));

        $this->list->filterByIsApproved(0);
        $this->assertEquals(1, $this->list->getTotal());
        $results = $this->list->get();
        $this->assertEquals(1, count($results));
    }

}
 