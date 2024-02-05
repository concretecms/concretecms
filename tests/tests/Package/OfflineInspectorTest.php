<?php

namespace Concrete\Tests\Package;

use Concrete\Core\Package\Offline\Exception;
use Concrete\Core\Package\Offline\Inspector;
use Concrete\Core\Package\Offline\PackageInfo;
use Concrete\Core\Support\Facade\Application;
use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OfflineInspectorTest extends TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    /**
     * @var \Concrete\Core\Package\Offline\Inspector
     */
    private static $inspector;

    public static function setupBeforeClass():void
    {
        self::$app = Application::getFacadeApplication();
        self::$inspector = self::$app->build(Inspector::class);
    }

    public static function validPackagesProvider()
    {
        return [
            ['good01.php', ['handle' => 'nice_legacy', 'version' => '0.9.1', 'name' => 'Legacy package!', 'description' => 'This is a nice legacy package.', 'minimumCoreVersion' => '5.5']],
            ['good02.php', ['handle' => 'nice_modern', 'version' => '1.2', 'name' => '5.7+ package!', 'description' => 'This is a nice 5.7+ package.', 'minimumCoreVersion' => '8.5']],
            ['good03.php', ['handle' => 'nice_modern', 'version' => '1.2', 'name' => '5.7+ package!', 'description' => 'This is a nice 5.7+ package.', 'minimumCoreVersion' => '5.7.0']],
        ];
    }

    /**
     * @param string $filename
     * @param array $expectedInfo
     */
    #[DataProvider('validPackagesProvider')]
    public function testExtractHandle($filename, array $expectedInfo)
    {
        $info = self::$inspector->inspectControllerFile(DIR_TESTS . '/assets/Package/offline/' . $filename);
        $this->assertInstanceOf(PackageInfo::class, $info);
        $this->assertSame($expectedInfo['handle'], $info->getHandle());
    }

    /**
     * @param string $filename
     * @param array $expectedInfo
     */
    #[DataProvider('validPackagesProvider')]
    public function testExtractVersion($filename, array $expectedInfo)
    {
        $info = self::$inspector->inspectControllerFile(DIR_TESTS . '/assets/Package/offline/' . $filename);
        $this->assertInstanceOf(PackageInfo::class, $info);
        $this->assertSame($expectedInfo['version'], $info->getVersion());
    }

    /**
     * @param string $filename
     * @param array $expectedInfo
     */
    #[DataProvider('validPackagesProvider')]
    public function testExtractName($filename, array $expectedInfo)
    {
        $info = self::$inspector->inspectControllerFile(DIR_TESTS . '/assets/Package/offline/' . $filename);
        $this->assertInstanceOf(PackageInfo::class, $info);
        $this->assertSame($expectedInfo['name'], $info->getName());
    }

    /**
     * @param string $filename
     * @param array $expectedInfo
     */
    #[DataProvider('validPackagesProvider')]
    public function testExtractDescription($filename, array $expectedInfo)
    {
        $info = self::$inspector->inspectControllerFile(DIR_TESTS . '/assets/Package/offline/' . $filename);
        $this->assertInstanceOf(PackageInfo::class, $info);
        $this->assertSame($expectedInfo['description'], $info->getDescription());
    }

    /**
     * @param string $filename
     * @param array $expectedInfo
     */
    #[DataProvider('validPackagesProvider')]
    public function testExtractMinimumCoreVersion($filename, array $expectedInfo)
    {
        $info = self::$inspector->inspectControllerFile(DIR_TESTS . '/assets/Package/offline/' . $filename);
        $this->assertInstanceOf(PackageInfo::class, $info);
        $this->assertSame($expectedInfo['minimumCoreVersion'], $info->getMinimumCoreVersion());
    }

    public static function invalidPackagesByFileProvider()
    {
        return [
            ['not-existing.php', Exception::ERRORCODE_FILENOTFOUND],
            ['wrong-handle1.php', Exception::ERRORCODE_MISMATCH_PACKAGEHANDLE],
            ['wrong-handle2.php', Exception::ERRORCODE_MISMATCH_PACKAGEHANDLE],
            ['wrong-class1.php', Exception::ERRORCODE_CONTROLLERCLASS_NOT_FOUND],
            ['wrong-class2.php', Exception::ERRORCODE_CONTROLLERCLASS_NOT_FOUND],
            ['multiple-classes1.php', Exception::ERRORCODE_MULTIPLE_CONTROLLECLASSES],
            ['multiple-classes2.php', Exception::ERRORCODE_MULTIPLE_CONTROLLECLASSES],
            ['wrong-namespace1.php', Exception::ERRORCODE_INVALID_NAMESPACENAME],
            ['missing-package-handle1.php', Exception::ERRORCODE_MISSING_PACKAGEHANDLE_PROPERTY],
            ['missing-package-handle2.php', Exception::ERRORCODE_MISSING_PACKAGEHANDLE_PROPERTY],
            ['missing-package-version1.php', Exception::ERRORCODE_MISSING_PACKAGEVERSION_PROPERTY],
            ['missing-package-version2.php', Exception::ERRORCODE_MISSING_PACKAGEVERSION_PROPERTY],
            ['missing-package-name1.php', Exception::ERRORCODE_MISSING_PACKAGENAME],
            ['missing-package-name2.php', Exception::ERRORCODE_MISSING_PACKAGENAME],
        ];
    }

    /**
     * @param string $filename
     * @param int $expectedExceptionCode
     */
    #[DataProvider('invalidPackagesByFileProvider')]
    public function testInvalidPackagesByFile($filename, $expectedExceptionCode)
    {
        try {
            self::$inspector->inspectControllerFile(DIR_TESTS . '/assets/Package/offline/' . $filename);
            $exception = null;
        } catch (Exception $x) {
            $exception = $x;
        }
        $this->assertNotNull($exception);
        $this->assertSame($expectedExceptionCode, $exception->getCode());
    }

    public static function invalidPackagesByContentProvider()
    {
        return [
            [null, Exception::ERRORCODE_BADPARAM],
            [false, Exception::ERRORCODE_BADPARAM],
        ];
    }

    /**
     * @param string $content
     * @param int $expectedExceptionCode
     */
    #[DataProvider('invalidPackagesByContentProvider')]
    public function testInvalidPackagesByContent($content, $expectedExceptionCode)
    {
        try {
            self::$inspector->inspectControllerContent($content);
            $exception = null;
        } catch (Exception $x) {
            $exception = $x;
        }
        $this->assertNotNull($exception);
        $this->assertSame($expectedExceptionCode, $exception->getCode());
    }
}
