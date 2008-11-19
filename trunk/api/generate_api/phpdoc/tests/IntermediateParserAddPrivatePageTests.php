<?php
/**
 * Unit Tests for the IntermediateParser->addPrivatePage() method
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
    define("PHPUnit_MAIN_METHOD", "IntermediateParserAddPrivatePageTests::main");
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
 * Unit Testing of the IntermediateParser's addPrivatePage() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.4.0a2
 */
class tests_IntermediateParserAddPrivatePageTests extends PHPUnit_Framework_TestCase {

    /**
     * phpDocumentor_setup object
     * @access private
     * @since 1.4.0a2
     */
    private $ps;
    /**
     * IntermediateParser object
     * @access private
     * @since 1.4.0a2
     */
    private $ip;
    /**
     * parserPage object
     * @access private
     * @since 1.4.0a2
     */
    private $pp;
    /**
     * parserData object
     * @access private
     * @since 1.4.0a2
     */
    private $pd;
    /**
     * path to file string
     * @access private
     * @since 1.4.0a2
     */
    private $path;

    /**
     * Runs the test methods of this class.
     * @access public
     * @static
     * @since 1.4.0a2
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("tests_IntermediateParserAddPrivatePageTests");
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
        $this->ip = new phpDocumentor_IntermediateParser();
        $this->pp = new parserPage();
        $this->pd = new ParserData;
        $this->pd->package = 'TESTING';
        $this->path = PHPDOCUMENTOR_BASE . 'TestFile.php';
        $this->ip->pages = array($this->path => $this->pd);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     * @access protected
     * @since 1.4.0a2
     */
    protected function tearDown() {
        unset($this->path);
        unset($this->pd);
        unset($this->pp);
        unset($this->ip);
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
     * Shows correct behavior for adding a private page object,
     * when the privatepages array already has an element
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorWhenPrivatePageArrayIsNotAlreadyEmpty() {
        $this->ip->privatepages = array($this->path => $this->pd);
        $this->ip->addPrivatePage($this->pp, $this->path);

        // verify parent attributes are set correctly
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->type, "page");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->id, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->file, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->sourceLocation, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->name, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->origName, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->category, "default");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->package, "default");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->subpackage, "");
        /** 
         * don't bother checking '[$this->path]->parent->parserVersion, 
         * because it will change between PhpDocumentor versions, 
         * and we don't want to keep it hand-updated in here 
         */
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->modDate, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->path, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->packageOutput, "");

        // now verify current page attributes are set correctly
        $this->assertEquals($this->ip->privatepages[$this->path]->elements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->_hasclasses, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->_hasinterfaces, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->privateelements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->classelements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->tutorial, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->privateclasselements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->links, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->clean, true);
        $this->assertEquals($this->ip->privatepages[$this->path]->docblock, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->_explicitdocblock, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->type, "page");
        $this->assertEquals($this->ip->privatepages[$this->path]->package, "TESTING");
    }
    /**
     * Shows correct behavior for adding a private page object,
     * when the privatepages array is completely empty
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorWhenPrivatePageArrayIsEmpty() {
        $this->ip->addPrivatePage($this->pp, $this->path);

        // verify parent attributes are set correctly
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->type, "page");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->id, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->file, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->sourceLocation, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->name, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->origName, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->category, "default");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->package, "default");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->subpackage, "");
        /** 
         * don't bother checking '[$this->path]->parent->parserVersion, 
         * because it will change between PhpDocumentor versions, 
         * and we don't want to keep it hand-updated in here 
         */
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->modDate, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->path, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->parent->packageOutput, "");

        // now verify current page attributes are set correctly
        $this->assertEquals($this->ip->privatepages[$this->path]->elements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->_hasclasses, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->_hasinterfaces, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->privateelements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->classelements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->tutorial, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->privateclasselements, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->links, array());
        $this->assertEquals($this->ip->privatepages[$this->path]->clean, true);
        $this->assertEquals($this->ip->privatepages[$this->path]->docblock, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->_explicitdocblock, "");
        $this->assertEquals($this->ip->privatepages[$this->path]->type, "page");
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
if (PHPUnit_MAIN_METHOD == "IntermediateParserAddPrivatePageTests::main") {
    tests_IntermediateParserAddPrivatePageTests::main();
}
?>
