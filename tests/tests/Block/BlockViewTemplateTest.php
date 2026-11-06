<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\View\BlockViewTemplate;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageList;
use Concrete\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

class BlockViewTemplateTest extends TestCase
{
    /**
     * The absolute paths of the block directories created in the application directory.
     *
     * @var string[]
     */
    protected $createdApplicationBlockDirectories = [];

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        foreach ($this->createdApplicationBlockDirectories as $directory) {
            $filesystem->deleteDirectory($directory);
        }
        $this->createdApplicationBlockDirectories = [];
        parent::tearDown();
    }

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

    public function testCoreBlockWithMissingCustomTemplateFallsBackToDefaultView()
    {
        $block = $this->getMockBlock('autonav', 'missing_template.php');
        $packageList = $this->getMockPackageList();
        $bv = new BlockViewTemplate($block, $packageList);

        $this->assertEquals('/path/to/server/concrete/blocks/autonav', $bv->getBaseURL());
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav', $bv->getBasePath());
        $this->assertEquals(DIR_BASE_CORE . '/blocks/autonav/view.php', $bv->getTemplate());
    }

    public function testApplicationBlockView(): void
    {
        $handle = 'test_application_block_view';
        $blockDirectory = $this->createApplicationBlock($handle, [FILENAME_BLOCK_VIEW]);

        $block = $this->getMockBlock($handle);
        $packageList = $this->getMockPackageList();
        $bv = new BlockViewTemplate($block, $packageList);

        $this->assertEquals('/path/to/server/application/blocks/' . $handle, $bv->getBaseURL());
        $this->assertEquals($blockDirectory, $bv->getBasePath());
        $this->assertEquals($blockDirectory . '/' . FILENAME_BLOCK_VIEW, $bv->getTemplate());
    }

    public function testApplicationBlockWithCustomTemplate(): void
    {
        $handle = 'test_application_block_template';
        $blockDirectory = $this->createApplicationBlock($handle, [
            FILENAME_BLOCK_VIEW,
            DIRNAME_BLOCK_TEMPLATES . '/custom.php',
        ]);

        $block = $this->getMockBlock($handle, 'custom.php');
        $packageList = $this->getMockPackageList();
        $bv = new BlockViewTemplate($block, $packageList);

        $this->assertEquals('/path/to/server/application/blocks/' . $handle, $bv->getBaseURL());
        $this->assertEquals($blockDirectory, $bv->getBasePath());
        $this->assertEquals($blockDirectory . '/' . DIRNAME_BLOCK_TEMPLATES . '/custom.php', $bv->getTemplate());
    }

    public function testApplicationBlockOverridesCoreBlock(): void
    {
        $handle = 'autonav';
        $blockDirectory = $this->createApplicationBlock($handle, [FILENAME_BLOCK_VIEW]);

        $block = $this->getMockBlock($handle);
        $packageList = $this->getMockPackageList();
        $bv = new BlockViewTemplate($block, $packageList);

        $this->assertEquals('/path/to/server/application/blocks/' . $handle, $bv->getBaseURL());
        $this->assertEquals($blockDirectory, $bv->getBasePath());
        $this->assertEquals($blockDirectory . '/' . FILENAME_BLOCK_VIEW, $bv->getTemplate());
    }

    /**
     * Create a block directory (and the given files in it) inside the application directory.
     *
     * The created files are automatically deleted when the test ends.
     *
     * @param string $handle the handle of the block type
     * @param string[] $files the paths (relative to the block directory) of the files to be created
     *
     * @return string the absolute path of the created block directory
     */
    protected function createApplicationBlock(string $handle, array $files): string
    {
        $blockDirectory = DIR_APPLICATION . '/' . DIRNAME_BLOCKS . '/' . $handle;
        if (is_dir($blockDirectory)) {
            $this->fail("The {$blockDirectory} directory already exists: please remove it in order to run this test.");
        }
        $this->createdApplicationBlockDirectories[] = $blockDirectory;
        foreach ($files as $file) {
            $path = $blockDirectory . '/' . $file;
            $directory = dirname($path);
            if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
                $this->fail("Failed to create the {$directory} directory.");
            }
            if (file_put_contents($path, '<?php') === false) {
                $this->fail("Failed to create the {$path} file.");
            }
        }

        return $blockDirectory;
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
