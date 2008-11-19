<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HighlightParserTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

/* You must add each method-level test suite file here */
require_once 'HighlightParserGetInlineTagsTests.php';

class tests_HighlightParserTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HighlightParser Unit Test Suites');
        /* You must add each method-level test suite name here */         
        $suite->addTestSuite('tests_HighlightParserGetInlineTagsTests');
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'HighlightParserTests::main') {
    tests_HighlightParserTests::main();
}
?>
