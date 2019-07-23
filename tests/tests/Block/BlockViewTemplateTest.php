<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockViewTemplate;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageList;
use PHPUnit_Framework_TestCase;

class BlockViewTemplateTest extends PHPUnit_Framework_TestCase
{
    // Core block, view.php, no custom template.
    public function testCoreBlockView()
    {
        $block = $this->getMockBlock('autonav');
        $packageList = $this->getMockPackageList();

        $bv = new BlockViewTemplate($block, $packageList);

        $baseURL = $bv->getBaseURL();
        $basePath = $bv->getBasePath();
        $template = $bv->getTemplate();

        $this->assertEquals('/path/to/server/concrete/blocks/autonav', $baseURL);
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav', $basePath);
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav/view.php', $template);
    }

    public function testCoreBlockWithCustomTemplateInCore()
    {
        $block = $this->getMockBlock('autonav', 'breadcrumb.php');
        $packageList = $this->getMockPackageList();
        $bv = new BlockViewTemplate($block, $packageList);

        $this->assertEquals('/path/to/server/concrete/blocks/autonav', $bv->getBaseURL());
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav', $bv->getBasePath());
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav/templates/breadcrumb.php', $bv->getTemplate());
    }

    public function testCoreBlockWithCustomTemplateDirectoryInCore()
    {
        $block = $this->getMockBlock('autonav', 'responsive_header_navigation');
        $packageList = $this->getMockPackageList();
        $bv = new BlockViewTemplate($block, $packageList);

        $this->assertEquals('/path/to/server/concrete/blocks/autonav/templates/responsive_header_navigation', $bv->getBaseURL());
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav/templates/responsive_header_navigation', $bv->getBasePath());
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav/templates/responsive_header_navigation/view.php', $bv->getTemplate());
    }

    public function testApplicationBlockView()
    {
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

    protected function getMockPackageList($handles = [])
    {
        // First, we create the package list we're going to use.
        $packages = [];
        foreach ($handles as $pkgHandle) {
            $pkg = $this->getMockBuilder(Package::class)
                ->disableOriginalConstructor()
                ->getMock();

            $pkg->expects($this->any())
                ->method('getPackageHandle')
                ->willReturn($pkgHandle);

            $packages[] = $pkg;
        }
        $packageList = $this->getMockBuilder(PackageList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageList->expects($this->any())
            ->method('getPackages')
            ->will($this->returnValue($packages));

        return $packageList;
    }
}
