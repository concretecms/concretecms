<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/3/14
 * Time: 7:12 AM.
 */
namespace Concrete\Tests\Core\File\Service;

use Core;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    protected $output1;

    protected function setUp()
    {
        $this->output1 = dirname(__FILE__) . '/output.jpg';
        if (file_exists($this->output1)) {
            unlink($this->output1);
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
        $service = Core::make('helper/image');
        $this->assertFalse(file_exists($this->output1));
        $service->create(
            $path, $this->output1, $width, $height, $fit
        );
        $this->assertTrue(file_exists($this->output1));
        $size = getimagesize($this->output1);
        $this->assertEquals($expectedWidth, $size[0]);
        $this->assertEquals($expectedHeight, $size[1]);
    }

    public function testStreamingImageOperations()
    {
    }
}
