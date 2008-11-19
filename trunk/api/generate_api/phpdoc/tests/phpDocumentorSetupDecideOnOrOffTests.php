<?php
/**
 * Unit Tests for the phpDocumentor_setup->decideOnOrOff() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.3.2
 */

/**
 * PHPUnit main() hack
 * 
 * "Call class::main() if this source file is executed directly."
 * @since 1.3.2
 */
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "phpDocumentorSetupDecideOnOrOffTests::main");
}
/**
 * TestCase
 * 
 * required by PHPUnit
 * @since 1.3.2
 */
require_once "PHPUnit/Framework/TestCase.php";
/**
 * TestSuite
 * 
 * required by PHPUnit
 * @since 1.3.2
 */
require_once "PHPUnit/Framework/TestSuite.php";

/**
 * Base directory of code
 * 
 * Needed by some of the objects being tested in the suites.
 * @since 1.4.1
 */
chdir(dirname(dirname(__FILE__)));
if (!defined("PHPDOCUMENTOR_BASE")) {
    define("PHPDOCUMENTOR_BASE", dirname(dirname(__FILE__)));
}

/**
 * PhpDocumentor Setup
 * 
 * required by PhpDocumentor to instantiate the environment
 * @since 1.3.2 
 */
require_once 'PhpDocumentor/phpDocumentor/Setup.inc.php';

/**
 * Unit Testing of the phpDocumentor_setup's decideOnOrOff() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.3.2
 */
class tests_phpDocumentorSetupDecideOnOrOffTests extends PHPUnit_Framework_TestCase {

    /**
     * phpDocumentor_setup object
     * @access private
     * @since 1.3.2
     */
    private $ps;

    /**
     * Runs the test methods of this class.
     * @access public
     * @static
     * @since 1.3.2
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("tests_phpDocumentorSetupDecideOnOrOffTests");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     * @access protected
     * @since 1.3.2
     */
    protected function setUp() {
        $GLOBALS['_phpDocumentor_install_dir'] = PHPDOCUMENTOR_BASE;
        $GLOBALS['_phpDocumentor_setting']['quiet'] = "true";
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     * @access protected
     * @since 1.3.2
     */
    protected function tearDown() {
        unset($this->ps);
    }


    /**
     * NOW LIST THE TEST CASES -------------------------------------------------------|
     */

    /**
     * normal, expected cases ------------------------------------------|
     */

    /**
     * demonstrate the correct behavior -----------------------|
     */

    /**
     * Shows correct behavior for handling the perfect expected "off" value
     * @since 1.3.2
     */
    public function testBasicOff() {
        $this->assertFalse(decideOnOrOff('off'));
    }
    /**
     * Shows correct behavior for handling the perfect expected "on" value
     * @since 1.3.2
     */
    public function testBasicOn() {
        $this->assertTrue(decideOnOrOff('on'));
    }

    /**
     * Shows correct behavior for handling the fuzzy "Off" value
     * @since 1.3.2
     */
    public function testFuzzyOffA() {
        $this->assertFalse(decideOnOrOff('Off'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "OFF" value
     * @since 1.3.2
     */
    public function testFuzzyOffB() {
        $this->assertFalse(decideOnOrOff('OFF'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "no" value
     * @since 1.3.2
     */
    public function testFuzzyNoA() {
        $this->assertFalse(decideOnOrOff('no'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "No" value
     * @since 1.3.2
     */
    public function testFuzzyNoB() {
        $this->assertFalse(decideOnOrOff('No'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "NO" value
     * @since 1.3.2
     */
    public function testFuzzyNoC() {
        $this->assertFalse(decideOnOrOff('NO'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "false" value
     * @since 1.3.2
     */
    public function testFuzzyFalseA() {
        $this->assertFalse(decideOnOrOff('false'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "False" value
     * @since 1.3.2
     */
    public function testFuzzyFalseB() {
        $this->assertFalse(decideOnOrOff('False'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "FALSE" value
     * @since 1.3.2
     */
    public function testFuzzyFalseC() {
        $this->assertFalse(decideOnOrOff('FALSE'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "0" value
     * @since 1.3.2
     */
    public function testFuzzyZero() {
        $this->assertFalse(decideOnOrOff(0));
    }

    /**
     * Shows correct behavior for handling the fuzzy "" value
     * @since 1.3.2
     */
    public function testFuzzyEmpty() {
        $this->assertTrue(decideOnOrOff(''));
    }
    /**
     * Shows correct behavior for handling the fuzzy "On" value
     * @since 1.3.2
     */
    public function testFuzzyOnA() {
        $this->assertTrue(decideOnOrOff('On'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "ON" value
     * @since 1.3.2
     */
    public function testFuzzyOnB() {
        $this->assertTrue(decideOnOrOff('ON'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "y" value
     * @since 1.3.2
     */
    public function testFuzzyYesA() {
        $this->assertTrue(decideOnOrOff('y'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "Y" value
     * @since 1.3.2
     */
    public function testFuzzyYesB() {
        $this->assertTrue(decideOnOrOff('Y'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "yes" value
     * @since 1.3.2
     */
    public function testFuzzyYesC() {
        $this->assertTrue(decideOnOrOff('yes'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "Yes" value
     * @since 1.3.2
     */
    public function testFuzzyYesD() {
        $this->assertTrue(decideOnOrOff('Yes'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "YES" value
     * @since 1.3.2
     */
    public function testFuzzyYesE() {
        $this->assertTrue(decideOnOrOff('YES'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "true" value
     * @since 1.3.2
     */
    public function testFuzzyTrueA() {
        $this->assertTrue(decideOnOrOff('true'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "True" value
     * @since 1.3.2
     */
    public function testFuzzyTrueB() {
        $this->assertTrue(decideOnOrOff('True'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "TRUE" value
     * @since 1.3.2
     */
    public function testFuzzyTrueC() {
        $this->assertTrue(decideOnOrOff('TRUE'));
    }
    /**
     * Shows correct behavior for handling the fuzzy "1" value
     * @since 1.3.2
     */
    public function testFuzzyOne() {
        $this->assertTrue(decideOnOrOff(1));
    }
                
    /**
     * END OF "demonstrate the correct behavior" --------------|
     */
    /**
     * END OF "normal, expected cases" ---------------------------------|
     */


    /**
     * odd, edge cases -------------------------------------------------|
     */
     
    /**
     * Shows correct behavior for handling an odd "  " value
     * @since 1.3.2
     */
    public function testUnexpectedSpaces() {
        $this->assertTrue(decideOnOrOff('  '));
    }
    /**
     * Shows correct behavior for handling an odd NULL value
     * @since 1.3.2
     */
    public function testUnexpectedNull() {
        $this->assertFalse(decideOnOrOff());
    }
    /**
     * Shows correct behavior for handling an odd "-1" value
     * @since 1.3.2
     */
    public function testUnexpectedNegative() {
        $this->assertFalse(decideOnOrOff(-1));
    }
    /**
     * Shows correct behavior for handling an odd "10" value
     * @since 1.3.2
     */
    public function testUnexpectedLargeNumber() {
        $this->assertFalse(decideOnOrOff(10));
    }
    /**
     * Shows correct behavior for handling an odd "ash nazg durbatuluk" value
     * @since 1.3.2
     */
    public function testUnexpectedGreatLiterature() {
        $this->assertFalse(decideOnOrOff("ash nazg durbatuluk"));
    }

    /**
     * END OF "odd, edge cases" ----------------------------------------|
     */
     
    /**
     * END OF "NOW LIST THE TEST CASES" ----------------------------------------------|
     */ 
}

/**
 * PHPUnit main() hack
 * "Call class::main() if this source file is executed directly."
 * @since 1.3.2
 */
if (PHPUnit_MAIN_METHOD == "phpDocumentorSetupDecideOnOrOffTests::main") {
    tests_phpDocumentorSetupDecideOnOrOffTests::main();
}
?>

 	  	 
