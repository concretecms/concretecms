<?php
/**
 * Unit Tests for the HighlightParser->getInlineTags() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.4.0a2
 */

/**
 * PHPUnit main() hack
 * 
 * "Call class::main() if this source file is executed directly."
 * @since 1.4.0a2
 */
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "HighlightParserGetInlineTagsTests::main");
}
/**
 * TestCase
 * 
 * required by PHPUnit
 * @since 1.4.0a2
 */
require_once "PHPUnit/Framework/TestCase.php";
/**
 * TestSuite
 * 
 * required by PHPUnit
 * @since 1.4.0a2
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
 * @since 1.4.0a2 
 */
require_once 'PhpDocumentor/phpDocumentor/Setup.inc.php';

/**
 * Unit Testing of the HighlightParser's getInlineTags() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.4.0a2
 */
class tests_HighlightParserGetInlineTagsTests extends PHPUnit_Framework_TestCase {

    /**
     * phpDocumentor_setup object
     * @access private
     * @since 1.4.0a2
     */
    private $ps;
    /**
     * phpDocumentor_HighlightParser object
     * @access private
     * @since 1.4.0a2
     */
    private $hp;

    /**
     * Runs the test methods of this class.
     * @access public
     * @static
     * @since 1.4.0a2
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("tests_HighlightParserGetInlineTagsTests");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     * @access protected
     * @since 1.4.0a2
     */
    protected function setUp() {
        $GLOBALS['_phpDocumentor_install_dir'] = PHPDOCUMENTOR_BASE;
        $GLOBALS['_phpDocumentor_setting']['quiet'] = "on";

        $this->ps = new phpDocumentor_setup();
        $this->hp = new phpDocumentor_HighlightParser();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     * @access protected
     * @since 1.4.0a2
     */
    protected function tearDown() {
        unset($this->hp);
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
     * Shows correct behavior when called with no actual value
     * and no $endinternal flag arg
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorWhenGivenOneEmptyArg() {
        $this->assertEquals('',$this->hp->getInlineTags(''));
    }
    /**
     * Shows correct behavior when called with no actual value
     * and a FALSE $endinternal flag arg
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorWhenGivenOneEmptyArgAndFalse() {
        $this->assertEquals('',$this->hp->getInlineTags('', false));
    }
    /**
     * Shows correct behavior when called with no actual value
     * and a TRUE $endinternal flag arg
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorWhenGivenOneEmptyArgAndTrue() {
        $this->assertEquals('',$this->hp->getInlineTags('', true));
    }

    /**
     * END OF "demonstrate the correct behavior" --------------|
     */
    /**
     * END OF "normal, expected cases" ---------------------------------|
     * @todo write more "normal" test cases
     */


    /**
     * odd, edge cases -------------------------------------------------|
     */
    /**
     * END OF "odd, edge cases" ----------------------------------------|
     * @todo write some "edge" test cases
     */

    /**
     * END OF "NOW LIST THE TEST CASES" ----------------------------------------------|
     */   
}

/**
 * PHPUnit main() hack
 * "Call class::main() if this source file is executed directly."
 * @since 1.4.0a2
 */
if (PHPUnit_MAIN_METHOD == "HighlightParserGetInlineTagsTests::main") {
    tests_HighlightParserGetInlineTagsTests::main();
}
?>
