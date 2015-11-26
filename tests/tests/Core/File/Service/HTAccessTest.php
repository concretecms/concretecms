<?php
namespace Concrete\Tests\Core\File\Service;

use Core;

class HTAccessTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\File\Service\HTAccess
     */
    private $htaccessHelper;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->htaccessHelper = Core::make('helper/file/htaccess');
    }

    public static function tearDownAfterClass()
    {
        self::deleteHtaccess();
    }

    private static function deleteHtaccess()
    {
        $f = Core::make('helper/file/htaccess')->getHTAccessFilename();
        if (is_file($f)) {
            @unlink($f);
        }

        return file_exists($f) ? false : true;
    }

    public function providerEnablePrettyUrls()
    {
        $rules = $this->htaccessHelper->getRewriteRules(false);
        $rulesWithComments = $this->htaccessHelper->getRewriteRules(true);
        $oldRules = "<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteBase ".DIR_REL."/\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME}/index.html !-f\nRewriteCond %{REQUEST_FILENAME}/index.php !-f\nRewriteRule . ".DISPATCHER_FILENAME." [L]\n</IfModule>";
        $oldRulesWithComments = "# -- concrete5 urls start --\n$oldRules\n# -- concrete5 urls end --";

        return array(
            // .htaccess should be untouched
            array("", false, "", false),
            array("\n\n", false, "\n\n", false),
            array("\n#Something\n\n", false, "\n#Something\n\n", false),
            // Enable rewrite when .htaccess does not exist
            array(false, false, "$rulesWithComments\n", true),
            // Enable rewrite when existing .htaccess is empty
            array("", false, "$rulesWithComments\n", true),
            // Enable rewrite when existing .htaccess contains something
            array("#Something", false, "#Something\n\n$rulesWithComments\n", true),
            array("#Something\n#Something else\n\n   \n\n\n", false, "#Something\n#Something else\n\n$rulesWithComments\n", true),
            // Disable rewrite previous-style (unindented) rules
            array($oldRules, true, '', false),
            array($oldRulesWithComments, true, '', false),
            array("#before\n$oldRules\n#after", true, "#before\n#after\n", false),
            // Do no touch previous-style (unindented) rules
            array($oldRules, true, $oldRules, true),
            array($oldRulesWithComments, true, $oldRulesWithComments, true),
            array("#before\n$oldRules\n#after", true, "#before\n$oldRules\n#after", true),
            array("#before\n$oldRulesWithComments\n#after", true, "#before\n$oldRulesWithComments\n#after", true),
        );

        return $cases;
    }

    /**
     * @param string|false $original The original contents of the .htaccess file (false: non-existing .htaccess file)
     * @param bool $originalEnabled The original contents of the .htaccess file should contain the rewrite rules?
     * @param string $final The final contents of the .htaccess file
     * @param bool $finalEnabled The final contents of the .htaccess file should contain the rewrite rules?
     *
     * @dataProvider providerEnablePrettyUrls
     */
    public function testEnablePrettyUrls($original, $originalEnabled, $final, $finalEnabled)
    {
        // Initialize the initial .htaccess file
        if ($original === false) {
            $this->assertTrue(self::deleteHtaccess(), 'Deleting .htaccess file');
        } else {
            $this->assertTrue($this->htaccessHelper->saveHTAccess($original), 'Writing original contents of .htaccess file');
        }

        // Check if the initial .htaccess file is parsed correctly
        $this->assertSame($originalEnabled, $this->htaccessHelper->hasRewriteRules(), 'Checking if the original .htacces file is parsed correctly');

        // Update the .htaccess file
        $this->assertTrue($this->htaccessHelper->setCurrentRewriteRules($finalEnabled), 'Updating contents of .htaccess file');

        // Verify that the .htaccess file has been updated correctly and we can detect it
        $this->assertSame($finalEnabled, $this->htaccessHelper->hasRewriteRules(), 'Checking if the final .htacces file is parsed correctly');

        // Verify that the final .htaccess contents is the same as the one we expected
        $this->assertSame($final, $this->htaccessHelper->readHTAccess(), 'Comparing expected .htaccess contents vs obtained .htaccess contents');
    }
}
