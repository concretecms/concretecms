<?php

namespace Concrete\Tests\Core\Url\Service;

class PrettyUrlTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Url\Service\PrettyUrl
     */
    private $prettyUrlHelper;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->prettyUrlHelper = \Core::make('helper/url/pretty');
    }

    public static function tearDownAfterClass()
    {
        self::deleteHtaccess();
    }

    private static function getHtaccessFilename()
    {
        return DIR_BASE.'/.htaccess';
    }

    private static function deleteHtaccess()
    {
        $f = self::getHtaccessFilename();
        if (is_file($f)) {
            @unlink($f);
        }

        return file_exists($f) ? false : true;
    }

    private static function setHtaccessContents($contents)
    {
        return @file_put_contents(self::getHtaccessFilename(), $contents) !== false;
    }

    private static function getHtaccessContents()
    {
        $f = self::getHtaccessFilename();

        return is_file($f) ? @file_get_contents(self::getHtaccessFilename()) : '';
    }

    public function providerEnablePrettyUrls()
    {
        $rules = $this->prettyUrlHelper->getRewriteRules();
        $rulesWithComments = $this->prettyUrlHelper->getHtaccessText();
        $oldRules = "<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteBase ".DIR_REL."/\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME}/index.html !-f\nRewriteCond %{REQUEST_FILENAME}/index.php !-f\nRewriteRule . ".DISPATCHER_FILENAME." [L]\n</IfModule>";
        $oldRulesWithComments = "# -- concrete5 urls start --\n$oldRules\n# -- concrete5 urls end --";

        return array(
            // .htaccess should be untouched
            array("", false, ""),
            array("\n\n", false, "\n\n"),
            array("\n#Something\n\n", false, "\n#Something\n\n"),
            // Enable rewrite when .htaccess does not exist
            array(false, true, "$rulesWithComments\n"),
            // Enable rewrite when existing .htaccess is empty
            array("", true, "$rulesWithComments\n"),
            // Enable rewrite when existing .htaccess contains something
            array("#Something", true, "#Something\n\n$rulesWithComments\n"),
            array("#Something\n#Something else\n\n   \n\n\n", true, "#Something\n#Something else\n\n$rulesWithComments\n"),
            // Disable rewrite previous-style (unindented) rules
            array($oldRules, false, ''),
            array($oldRulesWithComments, false, ''),
            array("#before\n$oldRules\n#after", false, "#before\n#after\n"),
            // Do no touch previous-style (unindented) rules
            array($oldRules, true, $oldRules),
            array($oldRulesWithComments, true, $oldRulesWithComments),
            array("#before\n$oldRules\n#after", true, "#before\n$oldRules\n#after"),
            array("#before\n$oldRulesWithComments\n#after", true, "#before\n$oldRulesWithComments\n#after"),
        );

        return $cases;
    }

    /**
     * @dataProvider providerEnablePrettyUrls
     */
    public function testEnablePrettyUrls($original, $setEnabled, $final)
    {
        if ($original === false) {
            $this->assertTrue(self::deleteHtaccess(), 'Deleting .htaccess file');
        } else {
            $this->assertTrue(self::setHtaccessContents($original), 'Writing original contents of .htaccess file');
        }
        $this->assertTrue($this->prettyUrlHelper->updateHtaccessContents($setEnabled), 'Updating contents of .htaccess file');
        $this->assertSame($final, self::getHtaccessContents(), 'Comparing expected .htaccess contents vs obtained .htaccess contents');
    }
}
