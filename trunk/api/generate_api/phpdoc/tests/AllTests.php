<?php

/**
 * Master Unit Test Suite file for PhpDocumentor
 * 
 * This top-level test suite file organizes 
 * all class test suite files, 
 * so that the full suite can be run 
 * by PhpUnit or via "pear run-tests -u". 
 *
 * PHP versions 4 and 5
 *
 * @category Tools and Utilities
 * @package  phpDocumentor
 * @subpackage UnitTesting
 * @author   Chuck Burgess <ashnazg@php.net>
 * @license  http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version  CVS: $Id: AllTests.php,v 1.4 2007/09/30 01:41:46 ashnazg Exp $
 * @link     http://pear.php.net/PhpDocumentor
 * @since    1.4.0a2
 * @todo     CS cleanup - change package to PhpDocumentor
 */


/**
 * Check PHP version... PhpUnit v3+ requires at least PHP v5.1.4
 */
if (version_compare(PHP_VERSION, "5.1.4") < 0) {
    // Cannnot run test suites
    echo "Cannot run test suites... requires at least PHP v5.1.4.\n";
    exit(1);
}


/**
 * Derive the "main" method name
 * @internal PhpUnit would have to rename PHPUnit_MAIN_METHOD to PHPUNIT_MAIN_METHOD
 *           to make this usage meet the PEAR CS... we cannot rename it here.
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PhpDocumentor_AllTests::main');
}


/*
 * Files needed by PhpUnit
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';


/*
 * You must add each additional class-level test suite file here
 */
require_once 'phpDocumentorSetupTests.php';
require_once 'phpDocumentorTParserTests.php';
require_once 'IntermediateParserTests.php';
require_once 'HighlightParserTests.php';
require_once 'ParserClassTests.php';
require_once 'ParserPageTests.php';


/**
 * Master Unit Test Suite class for PhpDocumentor
 * 
 * This top-level test suite class organizes 
 * all class test suite files, 
 * so that the full suite can be run 
 * by PhpUnit or via "pear run-tests -u". 
 *
 * @category Tools and Utilities
 * @package  phpDocumentor
 * @subpackage UnitTesting
 * @author   Chuck Burgess <ashnazg@php.net>
 * @license  http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version  Release: @package_version@
 * @link     http://pear.php.net/PhpDocumentor
 * @since    1.4.0a2
 * @todo     CS cleanup - change package to PhpDocumentor
 */
class PhpDocumentor_AllTests
{

    /**
     * Launches the TextUI test runner
     *
     * @return void
     * @uses PHPUnit_TextUI_TestRunner
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }


    /**
     * Adds all class test suites into the master suite
     *
     * @return PHPUnit_Framework_TestSuite a master test suite
     *                                     containing all class test suites
     * @uses PHPUnit_Framework_TestSuite
     */ 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite(
            'PhpDocumentor Full Suite of Unit Tests');

        /*
         * You must add each additional class-level test suite name here
         */
        $suite->addTest(tests_phpDocumentorSetupTests::suite());
        $suite->addTest(tests_phpDocumentorTParserTests::suite());
        $suite->addTest(tests_IntermediateParserTests::suite());
        $suite->addTest(tests_HighlightParserTests::suite());
        $suite->addTest(tests_ParserClassTests::suite());
        $suite->addTest(tests_ParserPageTests::suite());
        return $suite;
    }
}

/**
 * Call the main method if this file is executed directly
 * @internal PhpUnit would have to rename PHPUnit_MAIN_METHOD to PHPUNIT_MAIN_METHOD
 *           to make this usage meet the PEAR CS... we cannot rename it here.
 */
if (PHPUnit_MAIN_METHOD == 'PhpDocumentor_AllTests::main') {
    PhpDocumentor_AllTests::main();
}

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
