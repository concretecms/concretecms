<?php

namespace Concrete\Tests\File\Image\Svg;

use Concrete\Core\File\Image\Svg\Sanitizer;
use Concrete\Core\File\Image\Svg\SanitizerOptions;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Concrete\Tests\TestCase;

class SanitizerTest extends TestCase
{
    /**
     * @var \Concrete\Core\File\Image\Svg\Sanitizer
     */
    protected static $sanitizer;

    /**
     * @var \Concrete\Core\File\Image\Svg\SanitizerOptions
     */
    protected static $sanitizerOptions;

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::setupBeforeClass()
     */
    public static function setupBeforeClass():void
    {
        parent::setUpBeforeClass();
        $app = Application::getFacadeApplication();
        self::$sanitizer = $app->build(Sanitizer::class);
        self::$sanitizerOptions = new SanitizerOptions();
        self::$sanitizerOptions
            ->setUnsafeElements('script script2')
            ->setElementAllowlist('script2')
            ->setUnsafeAttributes('onload onload2 onclick')
            ->setAttributeAllowlist('onload2');
    }

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function TearDown():void
    {
        Mockery::close();
    }

    /**
     * @return array
     */
    public static function provideSanitizeWithDefaultSettings()
    {
        return [
            ['<svg/>', '<svg></svg>'],
            ['<svg good="1" />', '<svg></svg>'],
            ['<svg><script>alert(1);</script></svg>', '<svg></svg>'],
            ['<svg><script2>alert(1);</script2></svg>', '<svg></svg>'],
            ['<svg onload="alert(1)" />', '<svg></svg>'],
            ['<svg foo="1" onload="alert(1)" bar="2" />', '<svg></svg>'],
            ['<svg foo="1" OnLoad="alert(1)" OnLoad2="alert(1)" bar="2" />', '<svg></svg>'],
            ['<svg><script></script><g onLoad="alert(1)"><rect /></g></svg>', '<svg>  <g>    <rect></rect>  </g></svg>'],
        ];
    }

    /**
     * @param string $input
     * @param string $expectedOutput
     * @dataProvider provideSanitizeWithDefaultSettings
     */
    public function testSanitizeWithDefaultSettings($input, $expectedOutput)
    {
        $sanitized = self::$sanitizer->sanitizeData($input, self::$sanitizerOptions);
        $lines = explode("\n", $sanitized);
        $this->assertMatchesRegularExpression('/^<\?xml\b[^>]*\?>$/', array_shift($lines));
        $xml = trim(implode('', $lines));

        static::assertSame($expectedOutput, $xml);
    }

    public function testLoadInvalidFile()
    {
        $this->expectException(\Concrete\Core\File\Image\Svg\SanitizerException::class);
        self::$sanitizer->sanitizeFile(__DIR__ . '/does-not-exist');
    }

    /**
     * @return array
     */
    public static function provideInvalidData()
    {
        return [
            ['<svg'],
        ];
    }

    /**
     *
     *
     * @param mixed $invalidSvgData
     * @dataProvider provideInvalidData
     */
    public function testInvalidData($invalidSvgData)
    {
        $this->expectException(\Concrete\Core\File\Image\Svg\SanitizerException::class);
        self::$sanitizer->sanitizeData($invalidSvgData, self::$sanitizerOptions);
    }

    /**
     * @return array
     */
    public static function provideCheckDataWithSafeSvg()
    {
        return [
            'minimal' => ['<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10" fill="red"/></svg>'],
            'with XML declaration' => ["<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path d=\"M0 0h24v24H0z\"/></svg>"],
            'with comments' => ["<?xml version=\"1.0\"?>\n<!-- Generator: Adobe Illustrator -->\n<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 100 100\" xml:space=\"preserve\"><!-- inner --><circle cx=\"50\" cy=\"50\" r=\"40\"/></svg>"],
            'nested elements' => ['<svg xmlns="http://www.w3.org/2000/svg"><g><title>T</title><desc>D</desc><path d="M0 0"/></g></svg>'],
        ];
    }

    /**
     * Safe SVG files must not be reported as containing nodes to be removed: that's what
     * makes the "reject" import action refuse them.
     *
     * @param string $svgData
     * @dataProvider provideCheckDataWithSafeSvg
     */
    public function testCheckDataAcceptsSafeSvg($svgData)
    {
        $this->assertSame([], self::$sanitizer->checkData($svgData));
    }

    /**
     * @return array
     */
    public static function provideCheckDataWithUnsafeSvg()
    {
        return [
            'script element' => ['<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>', 'elements'],
            'event attribute' => ['<svg xmlns="http://www.w3.org/2000/svg"><rect onclick="alert(1)" width="1" height="1"/></svg>', 'attributes'],
            // Detected by the enshrined/svg-sanitize library only
            'javascript: link' => ['<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><a xlink:href="javascript:alert(1)"><rect width="1" height="1"/></a></svg>', 'enshrined'],
            'foreignObject' => ['<svg xmlns="http://www.w3.org/2000/svg"><foreignObject><b xmlns="http://www.w3.org/1999/xhtml">hi</b></foreignObject></svg>', 'enshrined'],
        ];
    }

    /**
     * @param string $svgData
     * @param string $expectedKey
     * @dataProvider provideCheckDataWithUnsafeSvg
     */
    public function testCheckDataRejectsUnsafeSvg($svgData, $expectedKey)
    {
        $removedNodes = self::$sanitizer->checkData($svgData);
        $this->assertNotSame([], $removedNodes);
        $this->assertArrayHasKey($expectedKey, $removedNodes);
    }

    public function testShouldThrowIfFileDoesNotExist()
    {
        $this->expectException(\Concrete\Core\File\Image\Svg\SanitizerException::class);
        $filename = __DIR__ . '/test-file';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(false);
        $fs->shouldReceive('get')->never();
        $fs->shouldReceive('put')->never();
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename);
    }

    public function testShouldSaveIfNothingChangedButOtherFilename()
    {
        $filename = __DIR__ . '/test-file';
        $filename2 = __DIR__ . '/test-file-2';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($filename)->andReturn("<?xml version=\"1.0\"?>\n<svg/>\n");
        $fs->shouldReceive('put')->once()->with($filename2, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<svg></svg>\n");
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename, self::$sanitizerOptions, $filename2);
    }

}
