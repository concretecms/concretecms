<?php

declare(strict_types=1);

namespace Concrete\Tests\Page;

use Concrete\Core\Entity\Site\SkeletonTree;
use Concrete\Core\Page\PageList;
use Concrete\TestHelpers\Page\PageTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class PageListTest extends PageTestCase
{
    /**
     * @var \Concrete\Core\Page\PageList
     */
    protected $list;

    protected $pageData = [
        [
            'Test Page 1', false,
        ],
        [
            'Abracadabra', false,
        ],
        [
            'Brace Yourself', false, 'alternate',
        ],
        [
            'Foobler', '/test-page-1',
        ],
        [
            'Test Page 2', false,
        ],
        [
            'Holy Mackerel', false,
        ],
        [
            'Another Fun Page', false, 'alternate',
        ],
        [
            'Foo Bar', '/test-page-2',
        ],
        [
            'Test Trash', false,
        ],
        [
            'Foo Bar', '/test-trash',
        ],
        [
            'Test Page 3', false,
        ],
        [
            'Another Page', false, 'alternate', 'right_sidebar',
        ],
        [
            'More Testing', false, 'alternate',
        ],
        [
            'Foobler', '/another-fun-page', 'another',
        ],
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'PermissionAccessList',
            'PageTypeComposerFormLayoutSets',
            'PermissionAccessEntityTypes',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            \Concrete\Core\Entity\Attribute\Type::class,
            \Concrete\Core\Entity\Attribute\Category::class,
            \Concrete\Core\Entity\Page\Feed::class,
            SkeletonTree::class,
        ]);
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        \Concrete\Core\Attribute\Key\Category::add('collection');
        \Concrete\Core\Permission\Access\Entity\Type::add('page_owner', 'Page Owner');
        \Concrete\Core\Permission\Category::add('page');
        \Concrete\Core\Permission\Key\Key::add('page', 'view_page', 'View Page', '', 0, 0);
        \PageTemplate::add('left_sidebar', 'Left Sidebar');
        \PageTemplate::add('right_sidebar', 'Right Sidebar');
        \PageType::add([
            'handle' => 'alternate',
            'name' => 'Alternate',
        ]);
        \PageType::add([
            'handle' => 'another',
            'name' => 'Another',
        ]);

        $self = new static('');
        foreach ($self->pageData as $data) {
            $c = call_user_func_array([$self, 'createPage'], $data);
            $c->reindex();
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->list = new \Concrete\Core\Page\PageList();
        $this->list->ignorePermissions();
    }

    public function testGetUnfilteredTotal()
    {
        static::assertEquals(15, $this->list->getTotalResults());
    }

    public function testFilterByTypeNone()
    {
        $this->list->filterByPageTypeHandle('fuzzy');
        static::assertEquals(0, $this->list->getTotalResults());
    }

    public function testFilterByTypeValid1()
    {
        $this->list->filterByPageTypeHandle('basic');
        static::assertEquals(9, $this->list->getTotalResults());

        $pagination = $this->list->getPagination();
        static::assertEquals(9, $pagination->getTotalResults());
        $results = $pagination->getCurrentPageResults();
        static::assertCount(9, $results);
        static::assertInstanceOf('\Concrete\Core\Page\Page', $results[0]);
    }

    public function testFilterByTypeValid2()
    {
        $this->list->filterByPageTypeHandle(['alternate', 'another']);
        static::assertEquals(5, $this->list->getTotalResults());
    }

    public function testSortByIDAscending()
    {
        $this->list->sortByCollectionIDAscending();
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        static::assertEquals(1, $results[0]->getCollectionID());
        static::assertEquals(2, $results[1]->getCollectionID());
        static::assertEquals(3, $results[2]->getCollectionID());
    }

    public function testSortByNameAscending()
    {
        $this->list->sortByName();
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        static::assertEquals('Abracadabra', $results[0]->getCollectionName());
        static::assertEquals('Another Fun Page', $results[1]->getCollectionName());
        static::assertEquals('Another Page', $results[2]->getCollectionName());
        static::assertEquals('Brace Yourself', $results[3]->getCollectionName());
    }

    public function testFilterByKeywords()
    {
        $this->list->filterByKeywords('brac', true);
        $total = $this->list->getTotalResults();
        static::assertEquals(2, $total);
    }

    public function testItemsPerPage()
    {
        $pagination = $this->list->getPagination();
        $pagination->setMaxPerPage(2);
        $pages = $pagination->getCurrentPageResults();
        static::assertEquals(2, count($pages));
    }

    public function testPaginationObject()
    {
        $this->list->sortByCollectionIDAscending();
        $pagination = $this->list->getPagination();
        $pagination->setMaxPerPage(2);
        static::assertInstanceOf('\Concrete\Core\Search\Pagination\Pagination', $pagination);
        static::assertEquals(2, $pagination->getMaxPerPage());
        static::assertEquals(15, $pagination->getTotalResults());
        static::assertEquals(1, $pagination->getCurrentPage());
        static::assertFalse($pagination->hasPreviousPage());
        static::assertTrue($pagination->hasNextPage());
        static::assertTrue($pagination->haveToPaginate());
    }

    public function testExcludingAliasesAndBasicGet()
    {
        $subject = \Page::getByPath('/test-page-2');
        $parent = \Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);
        $this->list->sortBy('cID', 'desc');

        $results = $this->list->getResults();
        static::assertEquals(15, count($results));
        static::assertEquals('Foobler', $results[0]->getCollectionName());
    }

    public function testFilterByParentID()
    {
        $subject = \Page::getByPath('/test-page-2');
        $parent = \Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);
        $parent = \Page::getByPath('/another-fun-page');
        $this->list->filterByParentID($parent->getCollectionID());
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        static::assertEquals(1, count($results));
        static::assertEquals(1, $pagination->getTotalResults());
    }

    public function testFilterByPageTypeID()
    {
        $type = \Concrete\Core\Page\Type\Type::getByHandle('alternate');
        $this->list->filterByPageTypeID($type->getPageTypeID());
        $pagination = $this->list->getPagination();
        $results = $pagination->getCurrentPageResults();
        static::assertEquals(4, count($results));
    }

    public function testFilterByNumChildren()
    {
        $this->list->filterByNumberOfChildren(2, '>=');
        $results = $this->list->getResults();
        $ids = array_map(static function ($result) {
            return $result->getCollectionID();
        }, $results);

        // A function to reduce a list of pages into the minimum child count
        $minimumChild = static function ($carry, $value) {
            $childCount = $value->getNumChildren();

            return ($carry === null || $childCount < $carry) ? $childCount : $carry;
        };

        // Make sure there are no results with less than 2 children
        static::assertGreaterThanOrEqual(2, array_reduce($results, $minimumChild));
        static::assertContains(1, $ids);

        $subject = \Page::getByPath('/test-page-2');
        $parent = \Page::getByPath('/holy-mackerel');
        $subject->addCollectionAlias($parent);

        $nl = new \Concrete\Core\Page\PageList();
        $nl->ignorePermissions();
        $nl->includeAliases();
        $nl->filterByNumberOfChildren(1, '>=');

        // Make sure there are no results with less than 1 child
        static::assertGreaterThanOrEqual(1, array_reduce($nl->getResults(), $minimumChild));
    }

    public function testFilterByActiveAndSystem()
    {
        \SinglePage::addGlobal(\Config::get('concrete.paths.trash'));

        $c = \Page::getByPath('/test-trash');
        $c->moveToTrash();

        $results = $this->list->getResults();
        static::assertCount(13, $results);

        $this->list->includeSystemPages(); // This includes the items inside trash because we're stupid.
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(14, $totalResults);

        $pagination = $this->list->getPagination();
        static::assertEquals(14, $pagination->getTotalResults());
        $results = $this->list->getResults();
        static::assertCount(14, $results);

        $this->list->includeInactivePages();
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(16, $totalResults);
        $pagination = $this->list->getPagination();
        static::assertEquals(16, $pagination->getTotalResults());
        $results = $this->list->getResults();
        static::assertCount(16, $results);
    }

    public function testAliases()
    {
        $parent = \Page::getByPath('/test-page-2/foo-bar');
        $subject = \Page::getByPath('/another-fun-page');
        $subject->addCollectionAlias($parent);

        $pc = \Page::getByPath('/brace-yourself');
        $pc->move($parent);

        $page = $this->createPage('Page 2', $parent);
        $page->reindex();

        $this->list->filterByParentID($parent->getCollectionID());
        $this->list->includeAliases();
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(3, $totalResults);

        $this->list->filterByKeywords('Page');
        $totalResults = $this->list->getTotalResults(); // should get two.
        static::assertEquals(2, $totalResults);

        $nl = new \Concrete\Core\Page\PageList();
        $nl->includeAliases();
        $nl->ignorePermissions();
        $nl->getQueryObject()->addOrderBy('cv.cvName', 'asc');
        $nl->getQueryObject()->addOrderBy('p.cID', 'asc');
        $total = $nl->getPagination()->getTotalResults();
        $results = $nl->getPagination()->setMaxPerPage(10)->getCurrentPageResults();
        static::assertEquals(18, $total);
        static::assertCount(10, $results);
        static::assertTrue($results[2]->isAlias());
        static::assertEquals('Another Fun Page', $results[2]->getCollectionName());
        static::assertEquals($results[2]->getCollectionID(), $subject->getCollectionID());
        static::assertEquals(20, $results[2]->getCollectionPointerOriginalID());
        static::assertEquals(8, $results[2]->getCollectionID());
    }

    public function testIndexedSearch()
    {
        $c = \Page::getByPath('/another-fun-page');
        $c->update(['cDescription' => 'A page of all pages.']);
        $c->reindex();

        $this->list->filterByFulltextKeywords('Page');
        $this->list->sortByRelevance();
        $results = $this->list->getResults();
        static::assertCount(6, $results);

        $ids = array_map(static function ($c) { return $c->getCollectionID(); }, $results);
        static::assertContains($c->getCollectionID(), $ids);

        // $this->assertEquals(8, $results[0]->getCollectionID());
        static::assertGreaterThan(0, $results[0]->getPageIndexScore());
        static::assertGreaterThan(0, $results[1]->getPageIndexScore());
        static::assertEquals($results[1]->getPageIndexScore(), $results[2]->getPageIndexScore());
    }

    public function testFilterByName()
    {
        $this->list->filterByName('Brace Yourself', true);
        static::assertEquals(1, $this->list->getTotalResults());

        $nl = new \Concrete\Core\Page\PageList();
        $nl->ignorePermissions();
        $nl->filterByName('Foo', false);
        static::assertEquals(3, $nl->getTotalResults());
    }

    public function testFilterByPath()
    {
        $this->createPage('More Fun', '/test-page-1/foobler');

        $this->list->filterByPath('/test-page-1');
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(2, $totalResults);
        $nl = new \Concrete\Core\Page\PageList();
        $nl->ignorePermissions();
        $nl->filterByPath('/test-page-1', false);
        $pagination = $nl->getPagination();
        static::assertEquals(1, $pagination->getNBResults());
    }

    public function testFilterByMultiplePaths()
    {
        $this->createPage('More Fun', '/test-page-1/foobler');
        $this->createPage('Extreme Fun', '/test-page-2');

        $this->list->filterByPath('/test-page-1');
        $this->list->filterByPath('/test-page-2');
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(4, $totalResults);
    }

    public function testFilterByPathWithArray()
    {
        $this->createPage('More Fun', '/test-page-1/foobler');

        $this->list->filterByPath(['/test-page-1']);
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(18, $totalResults);
    }

    public function testFilterByPagesWithCustomStyles()
    {
        $this->list->filterByPagesWithCustomStyles();
        $this->list->filterByPagesWithCustomStyles();
        $totalResults = $this->list->getTotalResults();
        static::assertEquals(0, $totalResults);
    }

    public function testFilterByVersionAuthorUserIDNoMatch()
    {
        $this->list->filterByVersionAuthorUserID(99999);
        static::assertEquals(0, $this->list->getTotalResults());
    }

    public function testFilterByVersionAuthorUserIDMatch()
    {
        // Pages are created in tests with no logged-in user, so cvAuthorUID defaults to 0.
        $this->list->filterByVersionAuthorUserID(0);
        static::assertGreaterThan(0, $this->list->getTotalResults());
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

        static::assertEquals('blog', $pf->getHandle());
        static::assertEquals(1, $pf->getID());

        $pf->ignorePermissions();
        $pl = $pf->getPageListObject();
        static::assertInstanceOf(PageList::class, $pl);
        static::assertEquals(1, $pl->getTotalResults());

        $results = $pl->getResults();
        static::assertEquals('Foobler', $results[0]->getCollectionName());
    }
}
