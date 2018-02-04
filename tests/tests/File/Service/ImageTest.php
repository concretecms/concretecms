<?php

namespace Concrete\Tests\File\Service;

use Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\File\Service\Fixtures\TestStorageLocation;
use PHPUnit_Framework_TestCase;

class ImageTest extends PHPUnit_Framework_TestCase
{
    protected $output;

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

        $this->output = [
            'jpeg' => '/output.jpg',
            'png' => '/output.png',
        ];
        foreach ($this->output as $output) {
            if ($fsl->has($output)) {
                $fsl->delete($output);
            }
        }
    }

    public function legacyImageCreateDataProvider()
    {
        return [
            [
                400, 150, DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', 400, 300, false,
            ],
            [
                133, 50, DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', 310, 50, false,
            ],
            [
                90, 90, DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', 90, 90, true,
            ],
            [
                70, 70, DIR_BASE . '/concrete/config/install/packages/elemental_full/files/balloon.jpg', 70, 70, true,
            ],
        ];
    }

    /**
     * @dataProvider legacyImageCreateDataProvider
     *
     * @param mixed $expectedWidth
     * @param mixed $expectedHeight
     * @param mixed $path
     * @param mixed $width
     * @param mixed $height
     * @param mixed $fit
     */
    public function testLegacyImageCreate($expectedWidth, $expectedHeight, $path, $width, $height, $fit = false)
    {
        $sl = $this->storageLocation;
        $fsl = $sl->getFileSystemObject();
        $service = new \Concrete\Core\File\Image\BasicThumbnailer($sl);
        $service->setApplication(Application::getFacadeApplication());
        $service->setJpegCompression(80);
        $service->setPngCompression(9);
        foreach (['auto', 'png', 'jpeg'] as $format) {
            $service->setThumbnailsFormat($format);
            if ($format === 'auto') {
                $expectedFormat = preg_match('/\.p?jpe?g$/i', $path) ? 'jpeg' : 'png';
            } else {
                $expectedFormat = $format;
            }
            switch ($expectedFormat) {
                case 'jpeg':
                    $expectedType = IMAGETYPE_JPEG;
                    break;
                case 'png':
                    $expectedType = IMAGETYPE_PNG;
                    break;
                default:
                    $expectedType = '???';
                    break;
            }

            foreach ($this->output as $output) {
                $this->assertFalse($fsl->has($output), "{$output} should not exist");
            }

            $service->create($path, $this->output[$expectedFormat], $width, $height, $fit);
            $this->assertTrue($fsl->has($this->output[$expectedFormat], "{$this->output[$expectedFormat]} should exist"));
            list($width, $height, $type) = getimagesize(sys_get_temp_dir() . $this->output[$expectedFormat]);
            $fsl->delete($this->output[$expectedFormat]);
            $this->assertEquals($expectedWidth, $width, 'Invalid width');
            $this->assertEquals($expectedHeight, $height, 'Invalid height');
            $this->assertSame($expectedType, $type, "Wrong format for {$format}");
        }
    }

    public function testStreamingImageOperations()
    {
    }
}
