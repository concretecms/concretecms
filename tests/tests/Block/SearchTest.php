<?php

namespace Concrete\Tests\Block;

use BlockType;
use Concrete\Core\Attribute\Key\Category as AttributeCategory;
use Concrete\Block\Search\Controller;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\TestHelpers\Block\BlockTypeTestCase;

class SearchTest extends BlockTypeTestCase
{
    protected $btHandle = 'search';

    protected $requestData = [
        'lipsum' => [
            'title' => 'Lorem ipsum dolor sit amet',
            'buttonText' => 'Search',
        ],
    ];

    protected $expectedRecordData = [
        'lipsum' => [
            'title' => 'Lorem ipsum dolor sit amet',
            'buttonText' => 'Search',
            'baseSearchPath' => '',
            'search_all' => 0,
            'allow_user_options' => 0,
            'postTo_cID' => 0,
            'resultsURL' => '',
        ],
    ];

    /** @var Request */
    private $origRequest;

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();

        AttributeCategory::add('collection');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'CollectionVersions',
            'Pages',
            'PageTypes',
            'PageSearchIndex',
            'PermissionAccessEntityTypes',
            'PermissionKeys',
            'PermissionKeyCategories',
            'PagePermissionAssignments',
            'BlockPermissionAssignments',
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
            'Concrete\Core\Entity\Attribute\Category',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Key\PageKey',
            'Concrete\Core\Entity\Attribute\Value\PageValue',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->origRequest = Request::getInstance();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Request::setInstance($this->origRequest);
    }

    public function testSearchWithUnexpectedQuery(): void
    {
        // Tests only that the code does not cause any errors/exceptions
        $this->expectNotToPerformAssertions();

        $this->runSearch(['query' => ['foo' => 'bar']]);
    }

    public function testSearchWithUnexpectedSearchPath(): void
    {
        // Tests only that the code does not cause any errors/exceptions
        $this->expectNotToPerformAssertions();

        $this->runSearch([
            'query' => 'test',
            'search_paths' => [['foo' => 'bar']]
        ]);
    }

    public function testSearchWithMultipleSearchPaths(): void
    {
        // Tests only that the code does not cause any errors/exceptions
        $this->expectNotToPerformAssertions();

        $this->runSearch([
            'query' => 'test',
            'search_paths' => ['/foo', '/bar']
        ]);
    }

    private function runSearch($params): void
    {
        $request = new Request($params);
        $request->setCurrentPage(Page::getByID(1));
        Request::setInstance($request);

        $btc = $this->getBlockController();

        $btc->view();
    }

    private function getBlockController(): Controller
    {
        $bt = BlockType::installBlockType($this->btHandle);
        $btx = BlockType::getByID(1);
        $class = $btx->getBlockTypeClass();
        $btc = new $class();
        $btc->setApplication(Application::getFacadeApplication());

        return $btc;
    }
}
