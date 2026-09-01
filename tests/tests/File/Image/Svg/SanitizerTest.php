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

    /**
     * SVGs that contain nothing unsafe, in the serialization forms real authoring tools emit.
     *
     * @return array
     */
    public static function provideCleanSvgData()
    {
        return [
            'indented, self-closing, with declaration' => [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\">\n    <circle cx=\"50\" cy=\"50\" r=\"40\"/>\n</svg>\n",
            ],
            'minified, no declaration' => [
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40"/></svg>',
            ],
            'no self-closing tags' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40"></circle></svg>',
            ],
            'nested groups and metadata' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><title>t</title><g id="a"><rect width="10" height="10"/></g></svg>',
            ],
        ];
    }

    /**
     * checkData() drives ACTION_REJECT in SvgProcessor::validate(), so anything it reports rejects the
     * upload with "The SVG file contains elements that could be potentially harmful."
     *
     * The enshrined library re-serializes through its own DOMDocument - preserveWhiteSpace = false,
     * formatOutput = true, saveXML() with LIBXML_NOEMPTYTAG - so its output never matches ours byte
     * for byte. Comparing the two strings flagged every SVG ever uploaded.
     *
     * @dataProvider provideCleanSvgData
     *
     * @param string $svg
     */
    public function testCleanSvgIsNotReportedAsHarmful($svg)
    {
        $this->assertSame(
            [],
            self::$sanitizer->checkData($svg),
            'A clean SVG must not be reported as containing anything to remove.'
        );
    }

    /**
     * @return array
     */
    public static function provideEnshrinedOnlyThreats()
    {
        return [
            'javascript: href' => [
                '<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript:alert(1)"><circle r="4"/></a></svg>',
            ],
            'javascript: xlink:href' => [
                '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><a xlink:href="javascript:alert(1)"><circle r="4"/></a></svg>',
            ],
        ];
    }

    /**
     * The reason the enshrined check exists: our own allowlist/blocklist pass does not look at URI
     * schemes, so a javascript: href is reported by the library and by nothing else.
     *
     * @dataProvider provideEnshrinedOnlyThreats
     *
     * @param string $svg
     */
    public function testEnshrinedOnlyThreatIsStillReported($svg)
    {
        $removedNodes = self::$sanitizer->checkData($svg);

        $this->assertArrayHasKey(
            'enshrined',
            $removedNodes,
            'A javascript: URI is only caught by the enshrined library, so it must still be reported.'
        );
        $this->assertGreaterThan(0, $removedNodes['enshrined']);
    }

    /**
     * Threats our own pass does catch must keep being attributed to it, not to the library.
     */
    public function testOurOwnPassStillReportsWhatItCatches()
    {
        $this->assertSame(
            ['elements' => ['script' => 1]],
            self::$sanitizer->checkData('<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><circle r="4"/></svg>')
        );
        $this->assertSame(
            ['attributes' => ['onload' => 1]],
            self::$sanitizer->checkData('<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><circle r="4"/></svg>')
        );
    }

    /**
     * Reporting aside, the javascript: URI must actually be gone from the sanitized output.
     */
    public function testJavascriptUriIsStrippedFromSanitizedOutput()
    {
        $clean = self::$sanitizer->sanitizeData(
            '<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript:alert(1)"><circle r="4"/></a></svg>'
        );

        $this->assertStringNotContainsString('javascript:', $clean);
    }
}
