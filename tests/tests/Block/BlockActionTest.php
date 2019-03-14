<?php

namespace Concrete\Tests\Block;

use Concrete\Block\ExpressEntryList\Controller;
use Concrete\Controller\Backend\Block\Action;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\MatchedRoute;
use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\RouteActionFactory;
use Concrete\Core\Routing\Router;
use Concrete\Core\Routing\SystemRouteList;
use Concrete\Core\Support\Facade\Facade;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Concrete\Core\Http\Request;
use Symfony\Component\Routing\RouteCollection;

class BlockActionTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test the add action
     */
    public function testBlockAddAction()
    {
        $c = $this->getMockBuilder(Page::class)
            ->getMock();
        $c->expects($this->any())
            ->method('getCollectionPath')
            ->willReturn('/path/to/page');
        $c->expects($this->any())
            ->method('getCollectionID')
            ->willReturn(123);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $a = $this->getMockBuilder(Area::class)
            ->disableOriginalConstructor()
            ->getMock();
        $a->expects($this->any())
            ->method('getAreaHandle')
            ->willReturn('Main');


        $blockType = $this->getMockBuilder(BlockType::class)
            ->getMock();
        $blockType->expects($this->any())
            ->method('getBlockTypeID')
            ->willReturn(4);

        $controller = new BlockController($blockType);
        $controller->setAreaObject($a);

        $view = new BlockView($blockType);
        $view->controller = $controller;
        $view->controller->runAction('add');
        $url = $view->action('add_form');

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/ccm/system/block/action/add/123/Main/4/add_form', (string)$url);

        $url = $view->action('add_form', '123');

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/ccm/system/block/action/add/123/Main/4/add_form/123', (string)$url);

        $url = $controller->getActionURL('delete_form', 'token');

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/ccm/system/block/action/add/123/Main/4/delete_form/token', (string)$url);
    }

    protected function tearDown()
    {
        $req = Request::getInstance();
        $req->clearcurrentPage();
        parent::tearDown();
    }

    protected function getTestBlockViewEditObject()
    {
        $c = $this->getMockBuilder(Page::class)
            ->getMock();
        $c->expects($this->any())
            ->method('getCollectionPath')
            ->willReturn('/about');
        $c->expects($this->any())
            ->method('getCollectionID')
            ->willReturn(50);

        $req = Request::getInstance();
        $req->setCurrentPage($c);

        $a = $this->getMockBuilder(Area::class)
            ->disableOriginalConstructor()
            ->getMock();
        $a->expects($this->any())
            ->method('getAreaHandle')
            ->willReturn('Second Column');

        $controller = $this->getMockBuilder(BlockController::class)
            ->getMock();

        $blockType = $this->getMockBuilder(BlockType::class)
            ->getMock();
        $blockType->expects($this->any())
            ->method('getPackageHandle')
            ->willReturn(null);

        $block = $this->getMockBuilder(Block::class)
            ->getMock();
        $block->expects($this->any())
            ->method('getBlockID')
            ->willReturn(1184);
        $block->expects($this->once())
            ->method('getInstance')
            ->willReturn($controller);
        $block->expects($this->any())
            ->method('getBlockTypeObject')
            ->willReturn($blockType);

        $block->expects($this->any())
            ->method('getBlockID')
            ->willReturn(1184);


        $controller = new BlockController($block);
        $controller->setAreaObject($a);

        $view = new BlockView($block);
        $view->controller = $controller;
        return $view;
    }

    /**
     * Test edit block action
     */
    public function testBlockEditAction()
    {
        $view = $this->getTestBlockViewEditObject();
        $view->controller->runAction('edit');
        $url = $view->action('get_control');

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/ccm/system/block/action/edit/50/Second%20Column/1184/get_control', (string)$url);

        $url = $view->controller->getActionURL('get_control', 4, 5, 6);

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/ccm/system/block/action/edit/50/Second%20Column/1184/get_control/4/5/6', (string)$url);
    }

    /**
     * Test view block action
     */
    public function testBLockViewAction()
    {
        $view = $this->getTestBlockViewEditObject();
        $view->controller->runAction('view');
        $url = $view->action('get_control');

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/about/get_control/1184', (string)$url);

        $url = $view->action('get_control', 114, 'xy');

        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/about/get_control/114/xy/1184', (string)$url);

    }

    public function blockControllerActionRoutingDataProvider()
    {
        return [
            ['/ccm/system/block/action/add/123/Main/4/add_form/', 'Concrete\Controller\Backend\Block\Action::add'],
            ['/ccm/system/block/action/edit/123/Main/1184/edit_control/', 'Concrete\Controller\Backend\Block\Action::edit'],
            ['/ccm/system/block/action/add/123/Main/4/add_form/123/x/', 'Concrete\Controller\Backend\Block\Action::add'],
            ['/ccm/system/block/action/edit/123/Main/1184/edit_control/123/x/', 'Concrete\Controller\Backend\Block\Action::edit'],
        ];
    }
    /**
     * @dataProvider blockControllerActionRoutingDataProvider
     * Test block routing to the add action method.
     */
    public function testBlockControllerActionRouting($path, $class)
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $list = new SystemRouteList();
        $list->loadRoutes($router);

        $context = new RequestContext();
        $context->fromRequest(Request::getInstance());
        $route = $router->getRouteByPath($path, $context);
        $action = $router->resolveAction($route);
        $this->assertEquals($class, $action->getControllerCallback());
    }
}
