<?php

namespace Concrete\Tests\File\Image\Svg;

use Concrete\Core\File\Image\Svg\Sanitizer;
use PHPUnit_Framework_TestCase;

class SanitizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\File\Image\Svg\Sanitizer
     */
    protected static $sanitizer;

    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$sanitizer = new Sanitizer();
    }

    /**
     * @return array
     */
    public function provideSanitizeWithDefaultSettings()
    {
        return [
            ['<svg/>', '<svg/>'],
            ['<svg good="1" />', '<svg good="1"/>'],
            ['<svg><script>alert(1);</script></svg>', '<svg/>'],
            ['<svg onload="alert(1)" />', '<svg/>'],
            ['<svg foo="1" onload="alert(1)" bar="2" />', '<svg foo="1" bar="2"/>'],
        ];
    }

    /**
     * @param string $input
     * @param string $expectedOutput
     * @dataProvider provideSanitizeWithDefaultSettings
     */
    public function testSanitizeWithDefaultSettings($input, $expectedOutput)
    {
        $sanitized = self::$sanitizer->sanitizeData($input);
        $lines = explode("\n", $sanitized);
        $this->assertSame('<?xml version="1.0"?>', array_shift($lines));
        $xml = trim(implode('', $lines));
        $this->assertSame($expectedOutput, $xml);
    }

    /**
     * @expectedException Concrete\Core\File\Image\Svg\SanitizerException
     */
    public function testLoadInvalidFile()
    {
        self::$sanitizer->sanitizeFile(__DIR__ . '/does-not-exist');
    }

    /**
     * @return array
     */
    public function provideInvalidData()
    {
        return [
            ['<svg']
        ];
    }
    
    /**
     * @expectedException Concrete\Core\File\Image\Svg\SanitizerException
     * @param mixed $invalidSvgData
     * @dataProvider provideInvalidData
     */
    public function testInvalidData($invalidSvgData)
    {
        self::$sanitizer->sanitizeData($invalidSvgData);
    }
}
