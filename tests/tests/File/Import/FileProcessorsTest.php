<?php

namespace Concrete\Tests\File\Import;

use Concrete\Core\Attribute\Category\CategoryService as AttributeCategoryService;
use Concrete\Core\Attribute\TypeFactory as AttributeTypeFactory;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\Processor\SvgProcessor;
use Concrete\Core\File\Import\Processor\XmlProcessor;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\File\FileStorageTestCase;
use Exception;
use Imagine\Image\Metadata\ExifMetadataReader;

class FileProcessorsTest extends FileStorageTestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected static $app;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected static $config;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'PermissionAccessEntityTypes',
            'FileImageThumbnailPaths',
            'FilePermissionAssignments',
            'ConfigStore',
            'Logs',
            'FileVersionLog',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
        ]);
    }

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        self::$app = Application::getFacadeApplication();
        self::$config = self::$app->make('config');
        self::$config->set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png;*.svg;*.xml;*.xslt');
        $attributeTypeFactory = self::$app->make(AttributeTypeFactory::class);
        $attributeCategoryService = self::$app->make(AttributeCategoryService::class);
        $fileAttributeCategory = $attributeCategoryService->getByHandle('file');
        if ($fileAttributeCategory === null) {
            $fileAttributeCategoryController = $attributeCategoryService->add('file');
        } else {
            $fileAttributeCategoryController = $fileAttributeCategory->getController();
        }
        $attributeType = $attributeTypeFactory->getByHandle('number');
        if ($attributeType === null) {
            $attributeType = $attributeTypeFactory->add('number', 'number');
        }
        if ($fileAttributeCategoryController->getAttributeKeyByHandle('width') === null) {
            $fileAttributeCategoryController->add($attributeType, ['akHandle' => 'width', 'akName' => 'Width']);
        }
        if ($fileAttributeCategoryController->getAttributeKeyByHandle('height') === null) {
            $fileAttributeCategoryController->add($attributeType, ['akHandle' => 'height', 'akName' => 'Height']);
        }
        self::$app->make('cache/request')->flush();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->getStorageLocation();
    }

    public function testImageAutorotator()
    {
        if (!ExifMetadataReader::isSupported()) {
            $this->markTestSkipped(ExifMetadataReader::getUnsupportedReason());
        }
        $file = DIR_TESTS . '/assets/File/Import/19x100-exif-rotated-6.jpg';
        $unrotatedWidth = 19;
        $unrotatedHeight = 100;
        $unrotatedWidthRange = range($unrotatedWidth - 1, $unrotatedWidth + 1);
        $unrotatedHeightRange = range($unrotatedHeight - 1, $unrotatedHeight + 1);
        $fileSHA1 = sha1_file($file);
        $index = 0;
        foreach ([false, true] as $enableExifRotation) {
            $fv = self::$config->withKey(
                'concrete.file_manager.images.use_exif_data_to_rotate_images',
                $enableExifRotation,
                function () use ($file, $index) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file, "test-autorotator-{$index}.jpg");
                }
            );
            // Check that the file hasn't changed
            $this->assertSame($fileSHA1, sha1_file($file));
            $width = (int) $fv->getAttribute('width');
            $height = (int) $fv->getAttribute('height');
            if ($enableExifRotation) {
                $this->assertContains($width, $unrotatedHeightRange);
                $this->assertContains($height, $unrotatedWidthRange);
            } else {
                $this->assertContains($width, $unrotatedWidthRange);
                $this->assertContains($height, $unrotatedHeightRange);
            }
            $index++;
        }
    }

    public function testImageSizeConstrain()
    {
        $file = DIR_TESTS . '/assets/File/Import/100x19.jpg';
        $originalWidth = 100;
        $originalHeight = 19;
        $maxWidth = 10;
        $maxHeight = 5;
        $fileSHA1 = sha1_file($file);
        $keys = self::$config->get('concrete.file_manager');
        $index = 0;
        foreach ([
            [false, false],
            [true, false],
            [false, true],
            [true, true],
        ] as $restrictDimensions) {
            $fv = self::$config->withKey(
                'concrete.file_manager',
                ['restrict_max_width' => $restrictDimensions[0] ? $maxWidth : null, 'restrict_max_height' => $restrictDimensions[1] ? $maxHeight : null] + $keys,
                function () use ($file, $index) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file, "test-constrain-{$index}.jpg");
                }
            );
            // Check that the file hasn't changed
            $this->assertSame($fileSHA1, sha1_file($file));
            $width = (int) $fv->getAttribute('width');
            $height = (int) $fv->getAttribute('height');
            if (!$restrictDimensions[0] && !$restrictDimensions[1]) {
                $this->assertSame($originalWidth, $width);
                $this->assertSame($originalHeight, $height);
            } else {
                if ($restrictDimensions[0]) {
                    $this->assertLessThanOrEqual($maxWidth, $width);
                }
                if ($restrictDimensions[1]) {
                    $this->assertLessThanOrEqual($maxHeight, $height);
                }
            }
            $index++;
        }
    }

    public function testLoadBrokenSvg()
    {
        $file = DIR_TESTS . '/assets/File/Import/malformed.svg';
        $fileSHA1 = sha1_file($file);
        try {
            self::$config->withKey(
                'concrete.file_manager.images.svg_sanitization.action',
                SvgProcessor::ACTION_DISABLED,
                function () use ($file) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file);
                }
            );
            $error = null;
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertSame($fileSHA1, sha1_file($file));
        $this->assertNull($error);
        try {
            self::$config->withKey(
                'concrete.file_manager.images.svg_sanitization.action',
                SvgProcessor::ACTION_CHECKVALIDITY,
                function () use ($file) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file);
                }
            );
            $error = null;
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertSame($fileSHA1, sha1_file($file));
        $this->assertInstanceOf(ImportException::class, $error);
        $this->assertSame(ImportException::E_FILE_INVALID, $error->getCode());
    }

    /**
     * @param string $contents the contents of the SVG file
     *
     * @return string the local path of a temporary SVG file
     */
    protected function createSvgFile($contents)
    {
        $file = DIR_FILES_UPLOADED_STANDARD . '/incoming/svg-test-' . uniqid() . '.svg';
        @mkdir(dirname($file), 0777, true);
        file_put_contents($file, $contents);

        return $file;
    }

    /**
     * @return array
     */
    public static function provideSafeSvg()
    {
        return [
            'minimal' => ['<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10" fill="red"/></svg>'],
            'with comments' => ["<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!-- Generator: Adobe Illustrator -->\n<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 100 100\" xml:space=\"preserve\"><circle cx=\"50\" cy=\"50\" r=\"40\"/></svg>"],
        ];
    }

    /**
     * @param string $contents
     * @dataProvider provideSafeSvg
     */
    public function testSvgProcessorAcceptsSafeSvgWhenConfiguredToReject($contents)
    {
        $file = $this->createSvgFile($contents);
        try {
            $error = null;
            try {
                self::$config->withKey(
                    'concrete.file_manager.images.svg_sanitization.action',
                    SvgProcessor::ACTION_REJECT,
                    function () use ($file) {
                        return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-svg-safe-' . uniqid() . '.svg');
                    }
                );
            } catch (Exception $x) {
                $error = $x;
            }
            $this->assertNull($error, $error === null ? '' : $error->getMessage());
        } finally {
            @unlink($file);
        }
    }

    public function testSvgProcessorRejectsHarmfulSvgWhenConfiguredToReject()
    {
        $file = DIR_TESTS . '/assets/File/Import/harmful.svg';
        $fileSHA1 = sha1_file($file);
        $error = null;
        try {
            self::$config->withKey(
                'concrete.file_manager.images.svg_sanitization.action',
                SvgProcessor::ACTION_REJECT,
                function () use ($file) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file);
                }
            );
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertSame($fileSHA1, sha1_file($file));
        $this->assertInstanceOf(ImportException::class, $error);
        $this->assertSame(ImportException::E_FILE_INVALID, $error->getCode());
    }

    /**
     * The enshrined/svg-sanitize library catches unsafe contents that our own allowlists
     * don't know about: those must be reported too, or "reject" would accept a file that
     * "sanitize" would have cleaned up.
     */
    public function testSvgProcessorRejectsJavascriptLinksWhenConfiguredToReject()
    {
        $file = $this->createSvgFile('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><a xlink:href="javascript:alert(1)"><rect width="1" height="1"/></a></svg>');
        try {
            $error = null;
            try {
                self::$config->withKey(
                    'concrete.file_manager.images.svg_sanitization.action',
                    SvgProcessor::ACTION_REJECT,
                    function () use ($file) {
                        return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-svg-js-' . uniqid() . '.svg');
                    }
                );
            } catch (Exception $x) {
                $error = $x;
            }
            $this->assertInstanceOf(ImportException::class, $error);
            $this->assertSame(ImportException::E_FILE_INVALID, $error->getCode());
        } finally {
            @unlink($file);
        }
    }

    public function testSvgProcessorSanitizesHarmfulSvgByDefault()
    {
        $file = DIR_TESTS . '/assets/File/Import/harmful.svg';
        $fileSHA1 = sha1_file($file);
        $fv = self::$config->withKey(
            'concrete.file_manager.images.svg_sanitization.action',
            SvgProcessor::ACTION_SANITIZE,
            function () use ($file) {
                return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-svg-sanitize-' . uniqid() . '.svg');
            }
        );
        // Check that the source file hasn't been touched
        $this->assertSame($fileSHA1, sha1_file($file));
        $contents = $fv->getFileContents();
        $this->assertStringNotContainsStringIgnoringCase('onclick', $contents);
        $this->assertStringContainsString('Text', $contents);
    }

    /**
     * @return string the local path of a temporary file containing an XML document with an
     * <?xml-stylesheet?> processing instruction pointing to an attacker-controlled stylesheet
     */
    protected function createXmlStylesheetFile()
    {
        $file = DIR_FILES_UPLOADED_STANDARD . '/incoming/xml-stylesheet-test-' . uniqid() . '.xml';
        @mkdir(dirname($file), 0777, true);
        file_put_contents(
            $file,
            "<?xml version=\"1.0\"?>\n<?xml-stylesheet type=\"text/xsl\" href=\"https://attacker.example/evil.xslt\"?>\n<root><child>hello</child></root>"
        );

        return $file;
    }

    public function testXmlProcessorSanitizesXmlStylesheetInstructionByDefault()
    {
        $file = $this->createXmlStylesheetFile();
        try {
            $fv = self::$config->withKey(
                'concrete.file_manager.documents.xml_sanitization.action',
                XmlProcessor::ACTION_SANITIZE,
                function () use ($file) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-xml-sanitize-' . uniqid() . '.xml');
                }
            );
            $contents = $fv->getFileContents();
        } finally {
            @unlink($file);
        }
        $this->assertStringNotContainsString('xml-stylesheet', $contents);
        $this->assertStringNotContainsString('attacker.example', $contents);
        $this->assertStringContainsString('<child>hello</child>', $contents);
    }

    /**
     * @return string the local path of a temporary file containing an XML document that libxml
     * can't parse (it contains a bare ampersand), with an xml-stylesheet processing instruction
     */
    protected function createMalformedXmlFile()
    {
        $file = DIR_FILES_UPLOADED_STANDARD . '/incoming/xml-malformed-test-' . uniqid() . '.xml';
        @mkdir(dirname($file), 0777, true);
        file_put_contents(
            $file,
            "<?xml version=\"1.0\"?>\n<?xml-stylesheet type=\"text/xsl\" href=\"https://attacker.example/evil.xslt\"?>\n<root><child>Ben & Jerry</child></root>"
        );

        return $file;
    }

    public function testXmlProcessorRejectsMalformedFilesUnlessDisabled()
    {
        foreach ([XmlProcessor::ACTION_SANITIZE, XmlProcessor::ACTION_CHECKVALIDITY, XmlProcessor::ACTION_REJECT] as $action) {
            $file = $this->createMalformedXmlFile();
            try {
                $error = null;
                try {
                    self::$config->withKey(
                        'concrete.file_manager.documents.xml_sanitization.action',
                        $action,
                        function () use ($file) {
                            return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-xml-malformed-' . uniqid() . '.xml');
                        }
                    );
                } catch (Exception $x) {
                    $error = $x;
                }
                $this->assertInstanceOf(ImportException::class, $error, "Action: {$action}");
                $this->assertSame(ImportException::E_FILE_MALFORMED_XML, $error->getCode(), "Action: {$action}");
                // The reason must survive: a generic "Invalid file." tells nobody anything
                $this->assertStringContainsString('well formed', $error->getMessage(), "Action: {$action}");
            } finally {
                @unlink($file);
            }
        }
    }

    public function testXmlProcessorImportsMalformedFilesWhenDisabled()
    {
        $file = $this->createMalformedXmlFile();
        try {
            $fv = self::$config->withKey(
                'concrete.file_manager.documents.xml_sanitization.action',
                XmlProcessor::ACTION_DISABLED,
                function () use ($file) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-xml-malformed-disabled-' . uniqid() . '.xml');
                }
            );
            $contents = $fv->getFileContents();
        } finally {
            @unlink($file);
        }
        $this->assertStringContainsString('xml-stylesheet', $contents);
    }

    public function testXmlProcessorRejectsXmlStylesheetInstructionWhenConfiguredTo()
    {
        $file = $this->createXmlStylesheetFile();
        try {
            $error = null;
            try {
                self::$config->withKey(
                    'concrete.file_manager.documents.xml_sanitization.action',
                    XmlProcessor::ACTION_REJECT,
                    function () use ($file) {
                        return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-xml-reject-' . uniqid() . '.xml');
                    }
                );
            } catch (Exception $x) {
                $error = $x;
            }
            $this->assertInstanceOf(ImportException::class, $error);
            $this->assertSame(ImportException::E_FILE_HARMFUL_CONTENTS, $error->getCode());
        } finally {
            @unlink($file);
        }
    }

    public function testXmlProcessorDoesNothingWhenDisabled()
    {
        $file = $this->createXmlStylesheetFile();
        $fileSHA1 = sha1_file($file);
        try {
            self::$config->withKey(
                'concrete.file_manager.documents.xml_sanitization.action',
                XmlProcessor::ACTION_DISABLED,
                function () use ($file) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file, 'test-xml-disabled-' . uniqid() . '.xml');
                }
            );
            $error = null;
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertSame($fileSHA1, sha1_file($file));
        $this->assertNull($error);
    }
}