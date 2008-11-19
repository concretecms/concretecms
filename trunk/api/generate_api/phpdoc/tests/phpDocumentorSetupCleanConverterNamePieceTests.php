<?php
/**
 * Unit Tests for the phpDocumentor_setup->cleanConverterNamePiece() method
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
    define("PHPUnit_MAIN_METHOD", "phpDocumentorSetupCleanConverterNamePieceTests::main");
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
 * Unit Testing of the phpDocumentor_setup's cleanConverterNamePiece() method
 * @package tests
 * @subpackage PhpDocumentorUnitTests
 * @author Chuck Burgess
 * @since 1.3.2
 */
class tests_phpDocumentorSetupCleanConverterNamePieceTests extends PHPUnit_Framework_TestCase {

    /**
     * phpDocumentor_setup object
     * @access private
     * @since 1.3.2
     */
    private $ps;
    /**
     * container for list of allowed special characters
     * in "primary" piece of converter names
     * @access private
     * @since 1.3.2
     */
    private $CHARACTERS_ALLOWED_IN_PRIMARY = '';
    /**
     * container for list of allowed special characters
     * in "secondary" piece of converter names
     * @access private
     * @since 1.3.2
     */
    private $CHARACTERS_ALLOWED_IN_SECONDARY = '\/';
    /**
     * container for list of allowed special characters
     * in "tertiary" piece of converter names
     * @access private
     * @since 1.3.2
     */
    private $CHARACTERS_ALLOWED_IN_TERTIARY = '.\/';

    /**
     * Runs the test methods of this class.
     * @access public
     * @static
     * @since 1.3.2
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("tests_phpDocumentorSetupCleanConverterNamePieceTests");
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
        $this->ps = new phpDocumentor_setup;
        $this->ps->setTitle("Unit Testing");    // this step is necessary to ensure ps->render is instantiated
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     * @access protected
     * @since 1.3.2
     */
    protected function tearDown() {
        unset($this->CHARACTERS_ALLOWED_IN_PRIMARY);
        unset($this->CHARACTERS_ALLOWED_IN_SECONDARY);
        unset($this->CHARACTERS_ALLOWED_IN_TERTIARY);
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
     * Shows correct behavior for handling the perfect expected "CHM" primary value
     * when called with one arg
     * @since 1.3.2
     */
    public function testNormalWithOneArgPrimaryCHM() {
        $this->assertEquals("CHM",              $this->ps->cleanConverterNamePiece("CHM"));
    }
    /**
     * Shows correct behavior for handling the perfect expected "HTML" primary value
     * when called with one arg
     * @since 1.3.2
     */
    public function testNormalWithOneArgPrimaryHTML() {
        $this->assertEquals("HTML",             $this->ps->cleanConverterNamePiece("HTML"));
    }
    /**
     * Shows correct behavior for handling the perfect expected "PDF" primary value
     * when called with one arg
     * @since 1.3.2
     */
    public function testNormalWithOneArgPrimaryPDF() {
        $this->assertEquals("PDF",              $this->ps->cleanConverterNamePiece("PDF"));
    }
    /**
     * Shows correct behavior for handling the perfect expected "XML" primary value
     * when called with one arg
     * @since 1.3.2
     */
    public function testNormalWithOneArgPrimaryXML() {
        $this->assertEquals("XML",              $this->ps->cleanConverterNamePiece("XML"));
    }

    /**
     * Shows correct behavior for handling the perfect expected "CHM" primary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalWithTwoArgsPrimaryCHM() {
        $this->assertEquals("CHM",              $this->ps->cleanConverterNamePiece("CHM",       $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "HTML" primary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalWithTwoArgsPrimaryHTML() {
        $this->assertEquals("HTML",             $this->ps->cleanConverterNamePiece("HTML",      $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "PDF" primary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalWithTwoArgsPrimaryPDF() {
        $this->assertEquals("PDF",              $this->ps->cleanConverterNamePiece("PDF",       $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "XML" primary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalWithTwoArgsPrimaryXML() {
        $this->assertEquals("XML",              $this->ps->cleanConverterNamePiece("XML",       $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }

    /**
     * Shows correct behavior for handling the perfect expected "frames" secondary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalSecondaryFrames() {
        $this->assertEquals("frames",           $this->ps->cleanConverterNamePiece("frames",    $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "Smarty" secondary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalSecondarySmarty() {
        $this->assertEquals("Smarty",           $this->ps->cleanConverterNamePiece("Smarty",    $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "default" secondary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalSecondaryDefault() {
        $this->assertEquals("default",          $this->ps->cleanConverterNamePiece("default",   $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "DocBook/peardoc2" secondary value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalSecondaryDocbookPeardoc2() {
        $this->assertEquals("DocBook/peardoc2", $this->ps->cleanConverterNamePiece("DocBook/peardoc2", $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }

    /**
     * Shows correct behavior for handling the perfect expected "default" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryDefault() {
        $this->assertEquals("default",          $this->ps->cleanConverterNamePiece("default",   $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "earthli" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryEarthli() {
        $this->assertEquals("earthli",          $this->ps->cleanConverterNamePiece("earthli",   $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "l0l33t" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryL0l33t() {
        $this->assertEquals("l0l33t",           $this->ps->cleanConverterNamePiece("l0l33t",    $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "phpdoc.de" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryPhpdocde() {
        $this->assertEquals("phpdoc.de",        $this->ps->cleanConverterNamePiece("phpdoc.de", $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "phphtmllib" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryPhphtmllib() {
        $this->assertEquals("phphtmllib",       $this->ps->cleanConverterNamePiece("phphtmllib",$this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "HandS" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryHands() {
        $this->assertEquals("HandS",            $this->ps->cleanConverterNamePiece("HandS",     $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "PEAR" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryPear() {
        $this->assertEquals("PEAR",             $this->ps->cleanConverterNamePiece("PEAR",      $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "PHP" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryPhp() {
        $this->assertEquals("PHP",              $this->ps->cleanConverterNamePiece("PHP",       $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }

    /**
     * Shows correct behavior for handling the perfect expected "DOM/default" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryDomDefault() {
        $this->assertEquals("DOM/default",      $this->ps->cleanConverterNamePiece("DOM/default",    $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "DOM/earthli" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryDomEarthli() {
        $this->assertEquals("DOM/earthli",      $this->ps->cleanConverterNamePiece("DOM/earthli",    $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "DOM/l0l33t" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryDomL0l33t() {
        $this->assertEquals("DOM/l0l33t",       $this->ps->cleanConverterNamePiece("DOM/l0l33t",     $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "DOM/phpdoc.de" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryDomPhpdocde() {
        $this->assertEquals("DOM/phpdoc.de",    $this->ps->cleanConverterNamePiece("DOM/phpdoc.de",  $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "DOM/phphtmllib" tertiary  value
     * when called with two args
     * @since 1.3.2
     */
    public function testNormalTertiaryDomPhphtmllib() {
        $this->assertEquals("DOM/phphtmllib",   $this->ps->cleanConverterNamePiece("DOM/phphtmllib", $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /**
     * Shows correct behavior for handling the perfect expected "b2evo.v-1-10" tertiary value
     * (an example of a user-defined template not packaged with PhpDocumentor)
     * when called with two args
     * @since 1.4.0
     */
    public function testUserDefinedTertiaryValue() {
        $this->assertEquals("b2evo.v-1-10",   $this->ps->cleanConverterNamePiece("b2evo.v-1-10", $this->CHARACTERS_ALLOWED_IN_TERTIARY));
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
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses one arg with value of "../../HTML"
     * @since 1.3.2
     */
    public function testPreventUpToParentPathingOnPrimaryWithOneArg() {
        $this->assertEquals("HTML",             $this->ps->cleanConverterNamePiece("../../HTML"));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "../../HTML"
     * @since 1.3.2
     */
    public function testPreventUpToParentPathingOnPrimaryWithTwoArgs() {
        $this->assertEquals("HTML",             $this->ps->cleanConverterNamePiece("../../HTML",   $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "../../frames"
     * @since 1.3.2
     */
    public function testPreventUpToParentPathingOnSecondary() {
        $this->assertEquals("//frames",         $this->ps->cleanConverterNamePiece("../../frames", $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "../../default"
     * @since 1.3.2
     */
    public function testPreventUpToParentPathingOnTertiary() {
        //    when '.' is allowed to remain, a '..' always returns false to avoid directory traversal
        $this->assertEquals(false,              $this->ps->cleanConverterNamePiece("../../default",$this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses one arg with value of "/var/log/HTML"
     * @since 1.3.2
     */
    public function testDoNotAllowTruePathingOnPrimaryWithOneArg() {
        $this->assertEquals("varlogHTML",       $this->ps->cleanConverterNamePiece("/var/log/HTML"));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "/var/log/HTML"
     * @since 1.3.2
     */
    public function testDoNotAllowTruePathingOnPrimaryWithTwoArgs() {
        $this->assertEquals("varlogHTML",       $this->ps->cleanConverterNamePiece("/var/log/HTML",   $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "/var/log/frames"
     * @since 1.3.2
     */
    public function testDoNotAllowTruePathingOnSecondary() {
        $this->assertEquals("/var/log/frames",  $this->ps->cleanConverterNamePiece("/var/log/frames", $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /** 
     * Verify no up-to-parent pathing is allowed...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "/var/log/default"
     * @since 1.3.2
     */
    public function testDoNotAllowTruePathingOnTertiary() {
        $this->assertEquals("/var/log/default", $this->ps->cleanConverterNamePiece("/var/log/default",$this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }



    /** 
     * Extreme example of messy input...
     * the resulting converter names are generally invalid.
     * This test uses one arg with value of "H/.T./M##L"
     * @since 1.3.2
     */
    public function testExtremeExampleButValidPrimaryWithOneArg() {
        $this->assertEquals("HTML",             $this->ps->cleanConverterNamePiece("H/.T./M##L"));
    }
    /** 
     * Extreme example of messy input...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "H/.T./M##L"
     * @since 1.3.2
     */
    public function testExtremeExampleButValidPrimaryWithTwoArgs() {
        $this->assertEquals("HTML",             $this->ps->cleanConverterNamePiece("H/.T./M##L", $this->CHARACTERS_ALLOWED_IN_PRIMARY));
    }
    /** 
     * Extreme example of messy input...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "....frames"
     * @since 1.3.2
     */
    public function testExtremeExampleButValidSecondary() {
        $this->assertEquals("frames",           $this->ps->cleanConverterNamePiece("....frames", $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /** 
     * Extreme example of messy input...
     * the resulting converter names are generally invalid.
     * This test uses two args with value of "..//.frames"
     * @since 1.3.2
     */
    public function testExtremeExampleAndInvalidSecondary() {
        $this->assertEquals("//frames",         $this->ps->cleanConverterNamePiece("..//.frames",     $this->CHARACTERS_ALLOWED_IN_SECONDARY));
    }
    /** 
     * Extreme example of messy input...
     * the resulting converter names are generally invalid.
     * This test uses two arg with value of "/./default/.##/"
     * @since 1.3.2
     */
    public function testExtremeExampleAndInvalidTertiaryA() {
        $this->assertEquals("/./default/./",    $this->ps->cleanConverterNamePiece("/./default/.##/", $this->CHARACTERS_ALLOWED_IN_TERTIARY));
    }
    /** 
     * Extreme example of messy input...
     * the resulting converter names are generally invalid.
     * This test uses two arg with value of "//default//"
     * @since 1.3.2
     */
    public function testExtremeExampleAndInvalidTertiaryB() {
        $this->assertEquals("//default//",      $this->ps->cleanConverterNamePiece("//default//",     $this->CHARACTERS_ALLOWED_IN_TERTIARY));
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
if (PHPUnit_MAIN_METHOD == "phpDocumentorSetupCleanConverterNamePieceTests::main") {
    tests_phpDocumentorSetupCleanConverterNamePieceTests::main();
}
?>
