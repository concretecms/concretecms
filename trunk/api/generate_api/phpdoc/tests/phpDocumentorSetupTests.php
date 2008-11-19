<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'phpDocumentorSetupTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

/* You must add each method-level test suite file here */
require_once 'phpDocumentorSetupCleanConverterNamePieceTests.php';
require_once 'phpDocumentorSetupDecideOnOrOffTests.php';

class tests_phpDocumentorSetupTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('phpDocumentorSetup Unit Test Suites');
        /* You must add each method-level test suite name here */
        $suite->addTestSuite('tests_phpDocumentorSetupCleanConverterNamePieceTests');
        $suite->addTestSuite('tests_phpDocumentorSetupDecideOnOrOffTests');
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'phpDocumentorSetupTests::main') {
    tests_phpDocumentorSetupTests::main();
}
?>
