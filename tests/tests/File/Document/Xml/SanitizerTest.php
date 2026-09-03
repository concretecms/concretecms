<?php

namespace Concrete\Tests\File\Document\Xml;

use Concrete\Core\File\Document\Xml\Sanitizer;
use Concrete\Core\File\Document\Xml\SanitizerException;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Concrete\Tests\TestCase;

class SanitizerTest extends TestCase
{
    /**
     * @var \Concrete\Core\File\Document\Xml\Sanitizer
     */
    protected static $sanitizer;

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::setupBeforeClass()
     */
    public static function setupBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $app = Application::getFacadeApplication();
        self::$sanitizer = $app->build(Sanitizer::class);
    }

    /**
     * {@inheritdoc}
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testXmlStylesheetProcessingInstructionIsStripped()
    {
        $data = "<?xml version=\"1.0\"?>\n<?xml-stylesheet type=\"text/xsl\" href=\"https://attacker.example/evil.xslt\"?>\n<root><child>hello</child></root>";

        $removedNodes = [];
        $sanitized = self::$sanitizer->sanitizeData($data, $removedNodes);

        $this->assertStringNotContainsString('xml-stylesheet', $sanitized);
        $this->assertStringNotContainsString('attacker.example', $sanitized);
        $this->assertArrayHasKey('processing_instructions', $removedNodes);
        $this->assertArrayHasKey('xml-stylesheet', $removedNodes['processing_instructions']);
        $this->assertSame(1, $removedNodes['processing_instructions']['xml-stylesheet']);
    }

    public function testOtherProcessingInstructionsAreAlsoStripped()
    {
        $data = "<?xml version=\"1.0\"?>\n<?some-other-pi data=\"1\"?>\n<root/>";

        $removedNodes = [];
        $sanitized = self::$sanitizer->sanitizeData($data, $removedNodes);

        $this->assertStringNotContainsString('some-other-pi', $sanitized);
        $this->assertArrayHasKey('processing_instructions', $removedNodes);
        $this->assertArrayHasKey('some-other-pi', $removedNodes['processing_instructions']);
    }

    public function testDoctypeIsStripped()
    {
        $data = "<?xml version=\"1.0\"?>\n<!DOCTYPE root [ <!ENTITY foo \"bar\"> ]>\n<root>&foo;</root>";

        $removedNodes = [];
        $sanitized = self::$sanitizer->sanitizeData($data, $removedNodes);

        $this->assertStringNotContainsString('DOCTYPE', $sanitized);
        $this->assertArrayHasKey('doctype', $removedNodes);
    }

    public function testCleanXmlIsUnaffected()
    {
        $data = "<?xml version=\"1.0\"?>\n<root><child>hello</child></root>";

        $removedNodes = [];
        $sanitized = self::$sanitizer->sanitizeData($data, $removedNodes);

        $this->assertSame([], $removedNodes);
        $this->assertStringContainsString('<child>hello</child>', $sanitized);
    }

    public function testCheckDataDoesNotModifyOriginal()
    {
        $data = "<?xml version=\"1.0\"?>\n<?xml-stylesheet href=\"evil.xslt\"?>\n<root/>";

        $removedNodes = self::$sanitizer->checkData($data);

        $this->assertArrayHasKey('processing_instructions', $removedNodes);
        $this->assertArrayHasKey('xml-stylesheet', $removedNodes['processing_instructions']);
    }

    public function testFileContainsValidXmlReturnsFalseForMalformedData()
    {
        $this->assertFalse(self::$sanitizer->dataContainsValidXml('<root'));
    }

    public function testFileContainsValidXmlReturnsTrueForValidData()
    {
        $this->assertTrue(self::$sanitizer->dataContainsValidXml('<root/>'));
    }

    public function testInvalidDataThrowsOnSanitize()
    {
        $this->expectException(SanitizerException::class);
        self::$sanitizer->sanitizeData('<root');
    }

    public function testShouldThrowIfFileDoesNotExist()
    {
        $this->expectException(SanitizerException::class);
        $filename = __DIR__ . '/does-not-exist.xml';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(false);
        $fs->shouldReceive('get')->never();
        $fs->shouldReceive('put')->never();
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename);
    }

    public function testSanitizeFileWritesToOutputFilename()
    {
        $filename = __DIR__ . '/test-file.xml';
        $filename2 = __DIR__ . '/test-file-2.xml';
        $fs = Mockery::mock(Filesystem::class);
        $fs->shouldReceive('isFile')->once()->with($filename)->andReturn(true);
        $fs->shouldReceive('get')->once()->with($filename)->andReturn("<?xml version=\"1.0\"?>\n<?xml-stylesheet href=\"evil.xslt\"?>\n<root/>\n");
        $fs->shouldReceive('put')->once()->andReturnUsing(function ($path, $contents) use ($filename2) {
            $this->assertSame($filename2, $path);
            $this->assertStringNotContainsString('xml-stylesheet', $contents);

            return true;
        });
        $sanitizer = new Sanitizer($fs);
        $sanitizer->sanitizeFile($filename, $filename2);
    }
}