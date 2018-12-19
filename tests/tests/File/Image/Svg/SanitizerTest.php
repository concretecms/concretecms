<?php

namespace Concrete\Tests\File\Image\Svg;

use Concrete\Core\File\Image\Svg\Sanitizer;
use Concrete\Core\File\Image\Svg\SanitizerOptions;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use PHPUnit_Framework_TestCase;

class SanitizerTest extends PHPUnit_Framework_TestCase
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
    public static function setupBeforeClass()
    {
        parent::setUpBeforeClass();
        $app = Application::getFacadeApplication();
        self::$sanitizer = $app->build(Sanitizer::class);
        self::$sanitizerOptions = new SanitizerOptions();
        self::$sanitizerOptions
            ->setUnsafeElements('script script2')
            ->setElementWhitelist('script2')
            ->setUnsafeAttributes('onload onload2 onclick')
            ->setAttributeWhitelist('onload2')
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        Mockery::close();
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
            ['<svg><script2>alert(1);</script2></svg>', '<svg><script2>alert(1);</script2></svg>'],
            ['<svg onload="alert(1)" />', '<svg/>'],
            ['<svg foo="1" onload="alert(1)" bar="2" />', '<svg foo="1" bar="2"/>'],
            ['<svg foo="1" OnLoad="alert(1)" OnLoad2="alert(1)" bar="2" />', '<svg foo="1" OnLoad2="alert(1)" bar="2"/>'],
            ['<svg><script></script><g onLoad="alert(1)"><rect /></g></svg>', '<svg><g><rect/></g></svg>'],
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
        $this->assertRegExp('/^<\?xml\b[^>]*\?>$/', array_shift($lines));
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
            ['<svg'],
        ];
    }

    /**
     * @expectedException Concrete\Core\File\Image\Svg\SanitizerException
     *
     * @param mixed $invalidSvgData
     * @dataProvider provideInvalidData
     */
    public function testInvalidData($invalidSvgData)
    {
        self::$sanitizer->sanitizeData($invalidSvgData, self::$sanitizerOptions);
    }

    /**
     * @expectedException Concrete\Core\File\Image\Svg\SanitizerException
     */
    public function testShouldThrowIfFileDoesNotExist()
    {
        $filename = __DIR__ . '/test-file';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(false);
        $fs->shouldReceive('get')->never();
        $fs->shouldReceive('put')->never();
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename);
    }

    public function testShouldNotSaveIfNothingChanged()
    {
        $filename = __DIR__ . '/test-file';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($filename)->andReturn("<?xml version=\"1.0\"?>\n<svg/>\n");
        $fs->shouldReceive('put')->never();
        $sanitizer = new Sanitizer($fs, self::$sanitizerOptions);
        $sanitizer->sanitizeFile($filename);
    }

    public function testShouldSaveIfNothingChangedButOtherFilename()
    {
        $filename = __DIR__ . '/test-file';
        $filename2 = __DIR__ . '/test-file-2';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($filename)->andReturn("<?xml version=\"1.0\"?>\n<svg/>\n");
        $fs->shouldReceive('put')->once()->with($filename2, "<?xml version=\"1.0\"?>\n<svg/>\n");
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename, self::$sanitizerOptions, $filename2);
    }

    public function testEncoding()
    {
        $input = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<svg test=\"\xE0\" onload=''>\xE8</svg>\n"; // 0xE0 === 'à' in iso-8859-1; 0xE8 === 'è' in iso-8859-1
        $output = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<svg test=\"\xE0\">\xE8</svg>\n"; // 0xE0 === 'à' in iso-8859-1; 0xE8 === 'è' in iso-8859-1
        $sanitized = self::$sanitizer->sanitizeData($input, self::$sanitizerOptions);
        $this->assertSame($output, $sanitized);
    }
}
