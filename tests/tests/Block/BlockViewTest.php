<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Support\Facade\Facade;
use PHPUnit_Framework_TestCase;

class BlockViewTest extends PHPUnit_Framework_TestCase
{
    /** @var \Concrete\Core\Application\Application */
    protected $app;

    public function setUp()
    {
        $this->app = Facade::getFacadeApplication();
    }

    public function testPreventRendering()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $director */
        $director = $this->app->make('director');
        $block = $this->getMockBlock('autonav');

        $listener = function ($event) {
            /** @var \Concrete\Core\Block\Events\BlockBeforeRender $event */
            $event->preventRendering();

            return $event;
        };

        $director->addListener('on_block_before_render', $listener);

        $view = new BlockView($block);

        ob_start();
        $view->renderViewContents([]);
        $this->assertEquals('', ob_get_flush());

        $director->removeListener('on_block_before_render', $listener);
    }

    protected function getMockBlock($handle, $bFilename = null)
    {
        $blockType = $this->getMockBuilder(BlockType::class)
            ->disableOriginalConstructor()
            ->getMock();
        $blockType->expects($this->any())
            ->method('getBlockTypeHandle')
            ->will($this->returnValue($handle));

        $controller = 'Concrete\\Block\\' . camelcase($handle) . '\\Controller';
        $controller = $this->getMockBuilder($controller)
            ->disableOriginalConstructor()
            ->getMock();

        $block = $this->getMockBuilder(Block::class)
            ->disableOriginalConstructor()
            ->getMock();
        $block->expects($this->any())
            ->method('getBlockTypeHandle')
            ->will($this->returnValue($handle));
        $block->expects($this->any())
            ->method('getInstance')
            ->will($this->returnValue($controller));
        $block->expects($this->any())
            ->method('getBlockTypeObject')
            ->will($this->returnValue($blockType));
        $block->expects($this->any())
            ->method('getBlockFilename')
            ->will($this->returnValue($bFilename));

        return $block;
    }
}
