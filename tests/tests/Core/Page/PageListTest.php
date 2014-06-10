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
        PageTemplate::add('right_sidebar', 'Left Sidebar');
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

    public function testGetUnfilteredTotal()
    {
        $this->assertEquals(5, $this->list->getTotal());
    }

    public function testFilterByTypeNone()
    {
        $this->list->filterByPageTypeHandle('fuzzy');
        $this->assertEquals(0, $this->list->getTotal());
    }

    public function testFilterByTypeValid1()
    {
        $this->list->filterByPageTypeHandle('basic');
        $this->assertEquals(3, $this->list->getTotal());
        $results = $this->list->getPage();
        $this->assertEquals(3, count($results));
        $this->assertInstanceOf('\Concrete\Core\Page\Page', $results[0]);
    }

    public function testFilterByTypeValid2()
    {
        $this->list->filterByPageTypeHandle('alternate');
        $this->assertEquals(1, $this->list->getTotal());
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
        $this->assertEquals('Brace Yourself', $results[1]->getCollectionName());
        $this->assertEquals('Foobler', $results[2]->getCollectionName());
        $this->assertEquals('Home', $results[3]->getCollectionName());
    }

    public function testFilterByKeywords()
    {
        $this->list->filterByKeywords('brac', true);
        $total = $this->list->getTotal();
        $this->assertEquals(2, $total);
    }


}
 