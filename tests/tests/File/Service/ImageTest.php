<?php

namespace Concrete\Tests\File\Service;

use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Image as ImageFacade;
use Concrete\TestHelpers\File\Service\Fixtures\TestStorageLocation;
use Concrete\Tests\TestCase;
use Imagine\Image\ImageInterface;

class ImageTest extends TestCase
{
    protected $output;

    /**
     * @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    protected $storageLocation;

    public function setUp():void
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

    public static function legacyImageCreateDataProvider()
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

    /**
     * The thumbnailer can work on an already opened image, so that the source image can be read as a
     * stream from a storage location (which may be remote) instead of from a local path, and a chain of
     * operations can be applied to it before the result is written back to the storage location.
     */
    public function testStreamingImageOperations(): void
    {
        $sourcePath = '/source.png';
        $sl = $this->storageLocation;
        $fsl = $sl->getFileSystemObject();
        if ($fsl->has($sourcePath)) {
            $fsl->delete($sourcePath);
        }
        $fsl->write($sourcePath, file_get_contents(DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png'));
        try {
            $service = new BasicThumbnailer($sl);
            $service->setApplication(Application::getFacadeApplication());
            $service->setPngCompression(9);
            $service->setThumbnailsFormat('png');

            // Read the source image as a stream: no local file path is involved.
            $stream = $fsl->readStream($sourcePath);
            static::assertIsResource($stream);
            $image = ImageFacade::read($stream);
            static::assertInstanceOf(ImageInterface::class, $image);
            $sourceSize = $image->getSize();

            // Apply an operation to the opened image, and let the thumbnailer work on the result.
            $image = $image->rotate(90);
            static::assertEquals($sourceSize->getHeight(), $image->getSize()->getWidth());
            static::assertEquals($sourceSize->getWidth(), $image->getSize()->getHeight());

            // The source file is not needed anymore: the thumbnailer works on the opened image.
            $fsl->delete($sourcePath);

            $output = $this->output['png'];
            static::assertFalse($fsl->has($output), "{$output} should not exist");
            $service->create($image, $output, 90, 90, true);
            static::assertTrue($fsl->has($output), "{$output} should exist");

            // The generated thumbnail can be read back as a stream too.
            $outputStream = $fsl->readStream($output);
            static::assertIsResource($outputStream);
            $outputContents = stream_get_contents($outputStream);
            fclose($outputStream);
            $fsl->delete($output);

            list($width, $height, $type) = getimagesizefromstring($outputContents);
            static::assertEquals(90, $width, 'Invalid width');
            static::assertEquals(90, $height, 'Invalid height');
            static::assertSame(IMAGETYPE_PNG, $type, 'Wrong format');
        } finally {
            foreach ([$sourcePath, $this->output['png']] as $path) {
                if ($fsl->has($path)) {
                    $fsl->delete($path);
                }
            }
        }
    }
}
