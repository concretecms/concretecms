<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/3/14
 * Time: 7:12 AM.
 */
namespace Concrete\Tests\Core\File\Service;
use Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration;
use Concrete\Tests\Core\File\Service\Fixtures\TestStorageLocation;
use Core;

class ImageTest extends \PHPUnit_Framework_TestCase
{

    protected $output1;

    /**
     * @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    protected $storageLocation;

    protected function setUp()
    {
        $local = new LocalConfiguration();
        $local->setRootPath(sys_get_temp_dir());
        $local->setWebRootRelativePath(sys_get_temp_dir());

        $sl = new TestStorageLocation();
        $sl->setConfigurationObject($local);
        $this->storageLocation = $sl;

        $fsl = $this->storageLocation->getFileSystemObject();

        $this->output1 = '/output.jpg';
        if ($fsl->has($this->output1)) {
            $fsl->delete($this->output1);
        }
    }

    public function legacyImageCreateDataProvider()
    {
        return array(
            array(
                400, 150, DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', 400, 300, false,
            ),
            array(
                133, 50, DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', 310, 50, false,
            ),
            array(
                90, 90, DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', 90, 90, true,
            ),
        );
    }

    /**
     * @dataProvider legacyImageCreateDataProvider
     */
    public function testLegacyImageCreate($expectedWidth, $expectedHeight, $path, $width, $height, $fit = false)
    {

        $sl = $this->storageLocation;
        $fsl = $sl->getFileSystemObject();
        $service = new \Concrete\Core\File\Image\BasicThumbnailer($sl);

        $this->assertFalse($fsl->has($this->output1));
        $service->create(
            $path, $this->output1, $width, $height, $fit
        );
        $this->assertTrue($fsl->has($this->output1));
        $size = getimagesize(sys_get_temp_dir() . $this->output1);
        $this->assertEquals($expectedWidth, $size[0]);
        $this->assertEquals($expectedHeight, $size[1]);
    }

    public function testStreamingImageOperations()
    {
    }
}
