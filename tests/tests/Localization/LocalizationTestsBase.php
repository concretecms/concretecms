<?php
namespace Concrete\Tests\Localization;

use Illuminate\Filesystem\Filesystem;

class LocalizationTestsBase extends \PHPUnit_Framework_TestCase
{
    protected static function getTranslationsFolder()
    {
        return DIR_APPLICATION . '/' . DIRNAME_LANGUAGES;
    }

    private static $applicationLanguagesAlreadyExisted = null;

    protected static function applicationLanguagesAlreadyExisted()
    {
        if (self::$applicationLanguagesAlreadyExisted === null) {
            $filesystem = new Filesystem();
            $translationsFolder = self::getTranslationsFolder();
            self::$applicationLanguagesAlreadyExisted = $filesystem->exists($translationsFolder);
        }

        return self::$applicationLanguagesAlreadyExisted;
    }

    public static function setUpBeforeClass()
    {
        if (static::applicationLanguagesAlreadyExisted()) {
            self::markTestSkipped('Cannot create the languages directory for the testing purposes. Please check permissions!');
        }
        $filesystem = new Filesystem();
        $translationsFolder = self::getTranslationsFolder();
        if ($filesystem->makeDirectory($translationsFolder) === false) {
            static::markTestSkipped('Cannot create the languages directory for the testing purposes. Please check permissions!');
        }
    }

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $translationsFolder = self::getTranslationsFolder();
        $filesystem->deleteDirectory($translationsFolder);
    }
}
