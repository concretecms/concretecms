<?php

namespace Concrete\Tests\File\Import;

use Concrete\Core\Attribute\Category\CategoryService as AttributeCategoryService;
use Concrete\Core\Attribute\TypeFactory as AttributeTypeFactory;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Import\Processor\SvgProcessor;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\File\FileStorageTestCase;
use Exception;

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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'Users',
            'PermissionAccessEntityTypes',
            'FileImageThumbnailTypes',
            'FileImageThumbnailPaths',
            'FilePermissionAssignments',
            'ConfigStore',
            'Logs',
            'FileVersionLog',
        ]);
        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\NumberSettings',
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
        ]);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$app = Application::getFacadeApplication();
        self::$config = self::$app->make('config');
        self::$config->set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png;*.svg');
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

    public function setUp()
    {
        parent::setUp();
        $this->getStorageLocation();
    }

    public function testImageAutorotator()
    {
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

    public function provideLoadHarmfulSvg()
    {
        return [
            [SvgProcessor::ACTION_DISABLED, true, false],
            [SvgProcessor::ACTION_CHECKVALIDITY, true, false],
            [SvgProcessor::ACTION_SANITIZE, true, true],
            [SvgProcessor::ACTION_REJECT, false],
            [SvgProcessor::ACTION_SANITIZE, true, false, '', 'onclick'],
            [SvgProcessor::ACTION_REJECT, true, false, '', 'onclick'],
        ];
    }

    /**
     * @dataProvider provideLoadHarmfulSvg
     *
     * @param string $action
     * @param bool $shouldImport
     * @param bool|null $shouldSanitize
     * @param string $allowedTags
     * @param string $allowedAttributes
     */
    public function testLoadHarmfulSvg($action, $shouldImport, $shouldSanitize = null, $allowedTags = '', $allowedAttributes = '')
    {
        static $index = 0;
        $index++;
        $file = DIR_TESTS . '/assets/File/Import/harmful.svg';
        $fileSHA1 = sha1_file($file);
        $fileContents = file_get_contents($file);
        try {
            $fv = self::$config->withKey(
                'concrete.file_manager.images.svg_sanitization',
                [
                    'action' => $action,
                    'allowed_tags' => $allowedTags,
                    'allowed_attributes' => $allowedAttributes,
                ] + self::$config->get('concrete.file_manager.images.svg_sanitization'),
                function () use ($file, $index) {
                    return self::$app->make(FileImporter::class)->importLocalFile($file, "test-harmful-{$index}.svg");
                }
            );
            $error = null;
        } catch (Exception $x) {
            $error = $x;
        }
        $this->assertSame($fileSHA1, sha1_file($file));
        if ($shouldImport) {
            $importedContents = $fv->getFileContents();
            if ($shouldSanitize) {
                $this->assertNotEmpty($importedContents);
                $this->assertNotSame($fileContents, $importedContents);
            } else {
                $this->assertSame($fileContents, $importedContents);
            }
        } else {
            $this->assertInstanceOf(ImportException::class, $error);
            $this->assertSame(ImportException::E_FILE_INVALID, $error->getCode());
        }
    }
}
