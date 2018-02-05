<?php

namespace Concrete\TestHelpers\Localization;

use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;

class LocalizationTestsBase extends PHPUnit_Framework_TestCase
{
    private static $applicationLanguagesRenamed = false;
    private static $applicationLanguagesCreated = false;

    public static function setUpBeforeClass()
    {
        self::$applicationLanguagesRenamed = false;
        $filesystem = new Filesystem();
        $translationsFolder = self::getTranslationsFolder();
        if ($filesystem->exists($translationsFolder)) {
            $originalTranslationsFolder = self::getOriginalTranslationsFolder();
            if ($filesystem->exists($originalTranslationsFolder)) {
                self::markTestSkipped('Both the languages and the languages-original directories exist.');

                return;
            }
            if (!$filesystem->move($translationsFolder, $originalTranslationsFolder)) {
                self::markTestSkipped('Unable to rename the the languages directory to languages-original.');

                return;
            }
            self::$applicationLanguagesRenamed = true;
        }
        if ($filesystem->makeDirectory($translationsFolder) === false) {
            self::markTestSkipped('Cannot create the languages directory for the testing purposes. Please check permissions!');

            return;
        }
        self::$applicationLanguagesCreated = true;
    }

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $translationsFolder = self::getTranslationsFolder();
        if (self::$applicationLanguagesCreated) {
            if ($filesystem->deleteDirectory($translationsFolder)) {
                self::$applicationLanguagesCreated = false;
            }
        }
        if (self::$applicationLanguagesRenamed) {
            if ($filesystem->move(self::getOriginalTranslationsFolder(), $translationsFolder)) {
                self::$applicationLanguagesRenamed = false;
            }
        }
    }

    protected static function getTranslationsFolder()
    {
        return DIR_APPLICATION . '/' . DIRNAME_LANGUAGES;
    }

    protected static function applicationLanguagesAlreadyExisted()
    {
        if (self::$applicationLanguagesAlreadyExisted === null) {
            $filesystem = new Filesystem();
            $translationsFolder = self::getTranslationsFolder();
            self::$applicationLanguagesAlreadyExisted = $filesystem->exists($translationsFolder);
        }

        return self::$applicationLanguagesAlreadyExisted;
    }

    private static function getOriginalTranslationsFolder()
    {
        return DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '-original';
    }
}
