<?php
/**
 * Unit Tests for the phpDocumentorTParser->getInlineTags() method
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
    define("PHPUnit_MAIN_METHOD", "phpDocumentorTParserGetInlineTagsTests::main");
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
 * Unit Testing of the phpDocumentorTParser's getInlineTags() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.4.0a2
 */
class tests_phpDocumentorTParserGetInlineTagsTests extends PHPUnit_Framework_TestCase {

    /**
     * phpDocumentor_setup object
     * @access private
     * @since 1.4.0a2
     */
    private $ps;
    /**
     * parserStringWithInlineTag object
     * @access private
     * @since 1.4.0a2
     */
    private $pswit;
    /**
     * phpDocumentorTParser object
     * @access private
     * @since 1.4.0a2
     */
    private $ptp;

    /**
     * Runs the test methods of this class.
     * @access public
     * @static
     * @since 1.4.0a2
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("tests_phpDocumentorTParserGetInlineTagsTests");
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
        $this->ptp = new phpDocumentorTParser();
        $this->pswit = new parserStringWithInlineTags();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     * @access protected
     * @since 1.4.0a2
     */
    protected function tearDown() {
        unset($this->pswit);
        unset($this->ptp);
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
     * Shows correct behavior for handling an inline link tag
     * e.g. {at-link URL link-text}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineLinkWhenParsePrivateOn() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "on";
        $link_tag = "Here's an {@link http://www.phpdoc.org Inline Link to a Hyperlink}\n";
        $this->pswit = $this->ptp->getInlineTags($link_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Here's an ");
        $this->assertEquals($this->pswit->value[1]->linktext, "Inline Link to a Hyperlink");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "link");
        $this->assertEquals($this->pswit->value[1]->value, "http://www.phpdoc.org");
    }
    /**
     * Shows correct behavior for handling an inline link tag
     * e.g. {at-link URL link-text}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineLinkWhenParsePrivateOff() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "off";
        $link_tag = "Here's an {@link http://www.phpdoc.org Inline Link to a Hyperlink}\n";
        $this->pswit = $this->ptp->getInlineTags($link_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Here's an ");
        $this->assertEquals($this->pswit->value[1]->linktext, "Inline Link to a Hyperlink");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "link");
        $this->assertEquals($this->pswit->value[1]->value, "http://www.phpdoc.org");
    }

    /**
     * Shows correct behavior for handling an inline example tag
     * e.g. {at-example path-to-file}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineExampleWhenParsePrivateOn() {
        // --parseprivate on
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "on";
        $example_tag = "Here's an external example file at {@example Bug-10871.php}\n";
        $this->pswit = $this->ptp->getInlineTags($example_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Here's an external example file at ");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "example");
        $this->assertEquals($this->pswit->value[1]->start, 1);
        $this->assertEquals($this->pswit->value[1]->end, "*");
        $this->assertEquals($this->pswit->value[1]->source, "");
        $this->assertEquals($this->pswit->value[1]->_class, "");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->value, "");
        $this->assertEquals($this->pswit->value[1]->path, "");
        $this->assertEquals($this->pswit->value[2], "\n");
    }
    /**
     * Shows correct behavior for handling an inline example tag
     * e.g. {at-example path-to-file}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineExampleWhenParsePrivateOff() {
        // --parseprivate off
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "off";
        $example_tag = "Here's an external example file at {@example Bug-10871.php}\n";
        $this->pswit = $this->ptp->getInlineTags($example_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Here's an external example file at ");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "example");
        $this->assertEquals($this->pswit->value[1]->start, 1);
        $this->assertEquals($this->pswit->value[1]->end, "*");
        $this->assertEquals($this->pswit->value[1]->source, "");
        $this->assertEquals($this->pswit->value[1]->_class, "");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->value, "");
        $this->assertEquals($this->pswit->value[1]->path, "");
        $this->assertEquals($this->pswit->value[2], "\n");
    }

    /**
     * Shows correct behavior for handling an inline source tag
     * e.g. {at-source}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineSourceWhenParsePrivateOn() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "on";
        $source_tag = "Also, here's the actual code: {@source}\n";
        $this->pswit = $this->ptp->getInlineTags($source_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Also, here's the actual code: ");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "source");
        $this->assertEquals($this->pswit->value[1]->start, 1);
        $this->assertEquals($this->pswit->value[1]->end, "*");
        $this->assertEquals($this->pswit->value[1]->source, "");
        $this->assertEquals($this->pswit->value[1]->_class, "");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->value, "");
        $this->assertEquals($this->pswit->value[2], "");
    }
    /**
     * Shows correct behavior for handling an inline source tag
     * e.g. {at-source}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineSourceWhenParsePrivateOff() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "off";
        $source_tag = "Also, here's the actual code: {@source}\n";
        $this->pswit = $this->ptp->getInlineTags($source_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Also, here's the actual code: ");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "source");
        $this->assertEquals($this->pswit->value[1]->start, 1);
        $this->assertEquals($this->pswit->value[1]->end, "*");
        $this->assertEquals($this->pswit->value[1]->source, "");
        $this->assertEquals($this->pswit->value[1]->_class, "");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->value, "");
        $this->assertEquals($this->pswit->value[2], "");
    }

    /**
     * Shows correct behavior for handling an inline tutorial tag
     * e.g. {at-tutorial path-to-pkgfile}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineTutorialWhenParsePrivateOn() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "on";
        $tutorial_tag = "And a tutorial {@tutorial userguide/tutorials/UserGuide/UserGuide.pkg}\n";
        $this->pswit = $this->ptp->getInlineTags($tutorial_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "And a tutorial ");
        $this->assertEquals($this->pswit->value[1]->linktext, "userguide/tutorials/UserGuide/UserGuide.pkg");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "tutorial");
        $this->assertEquals($this->pswit->value[1]->value, "userguide/tutorials/UserGuide/UserGuide.pkg");
        $this->assertEquals($this->pswit->value[2], "\n");
    }
    /**
     * Shows correct behavior for handling an inline tutorial tag
     * e.g. {at-tutorial path-to-pkgfile}
     * 
     * There should be NO difference in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineTutorialWhenParsePrivateOff() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "off";
        $tutorial_tag = "And a tutorial {@tutorial userguide/tutorials/UserGuide/UserGuide.pkg}\n";
        $this->pswit = $this->ptp->getInlineTags($tutorial_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "And a tutorial ");
        $this->assertEquals($this->pswit->value[1]->linktext, "userguide/tutorials/UserGuide/UserGuide.pkg");
        $this->assertEquals($this->pswit->value[1]->type, "inlinetag");
        $this->assertEquals($this->pswit->value[1]->inlinetype, "tutorial");
        $this->assertEquals($this->pswit->value[1]->value, "userguide/tutorials/UserGuide/UserGuide.pkg");
        $this->assertEquals($this->pswit->value[2], "\n");

    }

    /**
     * Shows correct behavior for handling an inline internal tag
     * e.g. {at-internal blah-blah-blah}
     * 
     * There SHOULD be differences in results due to --parseprivate flag
     * 
     * This test demonstrates PEAR Bug #10871
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineInternalWhenParsePrivateOn() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "on";
        $internal_tag = "{@internal Method name is not in camel caps format}}\n";
        $this->pswit = $this->ptp->getInlineTags($internal_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "Method name is not in camel caps format\n");
    }
    /**
     * Shows correct behavior for handling an inline internal tag
     * e.g. {at-internal blah-blah-blah}
     * 
     * There SHOULD be differences in results due to --parseprivate flag
     * 
     * @since 1.4.0a2
     */
    public function testShowCorrectBehaviorInlineInternalWhenParsePrivateOff() {
        $GLOBALS['_phpDocumentor_setting']['parseprivate'] = "off";
        $internal_tag = "{@internal Method name is not in camel caps format}}\n";
        $this->pswit = $this->ptp->getInlineTags($internal_tag);
        $this->assertEquals($this->pswit->type, "_string");
        $this->assertEquals($this->pswit->cache, "");
        $this->assertEquals($this->pswit->value[0], "\n");
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
if (PHPUnit_MAIN_METHOD == "phpDocumentorTParserGetInlineTagsTests::main") {
    tests_phpDocumentorTParserGetInlineTagsTests::main();
}
?>
