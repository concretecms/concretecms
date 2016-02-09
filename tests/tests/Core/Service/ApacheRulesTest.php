<?php
namespace Concrete\Tests\Core\Service;

class ApacheRulesTest extends \PHPUnit_Framework_TestCase
{
    private static $prepared = false;
    /**
     * @var \Concrete\Core\Service\Configuration\HTTP\ApacheConfigurator
     */
    private static $configurator;

    /**
     * @var \Concrete\Core\Service\Rule\RuleInterface
     */
    private static $prettyUrlRule;

    private static function prepareClass()
    {
        if (!self::$prepared) {
            $apache = \Core::make('\Concrete\Core\Service\HTTP\Apache', array(''));
            /* @var $apache \Concrete\Core\Service\HTTP\Apache */
            self::$configurator = $apache->getConfigurator();
            self::$prettyUrlRule = $apache->getGenerator()->getRule('pretty_urls');
            self::$prepared = true;
        }
    }

    public function detectPrettyUrlProvider()
    {
        self::prepareClass();
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

        return array(
            array(false, ''),
            array(true, <<<EOT
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase $DIR_REL/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteCond %{REQUEST_FILENAME}/index.php !-f
RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
            ),
            array(true, <<<EOT
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase $DIR_REL/
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME}/index.html !-f
	RewriteCond %{REQUEST_FILENAME}/index.php !-f
	RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
            ),
            array(true, <<<EOT
# -- concrete5 urls start --
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase $DIR_REL/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteCond %{REQUEST_FILENAME}/index.php !-f
RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
            ),
            array(true, <<<EOT
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase $DIR_REL/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteCond %{REQUEST_FILENAME}/index.php !-f
RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
# -- concrete5 urls end --
EOT
            ),
            array(true, <<<EOT
# -- concrete5 urls start --
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase $DIR_REL/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteCond %{REQUEST_FILENAME}/index.php !-f
RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
# -- concrete5 urls end --
EOT
            ),
            array(true, <<<EOT
# -- concrete5 urls start --
<IfModule mod_rewrite.c>
 RewriteEngine On
  RewriteBase $DIR_REL/
   RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME}/index.html !-f
     RewriteCond %{REQUEST_FILENAME}/index.php !-f
      RewriteRule . $DISPATCHER_FILENAME [L]
       </IfModule>
        # -- concrete5 urls end --
EOT
            ),
        );
    }
    /**
     *  @dataProvider detectPrettyUrlProvider
     */
    public function testDetectPrettyUrl($found, $htaccess)
    {
        $this->assertSame($found, self::$configurator->hasRule($htaccess, self::$prettyUrlRule));
    }

    public function addPrettyUrlProvider()
    {
        self::prepareClass();
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;
        $ruleWithoutComments = self::$prettyUrlRule->getCode();
        $ruleWithComments = trim(self::$prettyUrlRule->getCommentsBefore()."\n".$ruleWithoutComments."\n".self::$prettyUrlRule->getCommentsAfter());

        return array(
            array("", "$ruleWithComments\n"),
            array("\n\n\n\n", "$ruleWithComments\n"),
            array("Foo", "Foo\n\n$ruleWithComments\n"),
            array("Foo\n\n\n\n", "Foo\n\n$ruleWithComments\n"),
            array($ruleWithoutComments, $ruleWithoutComments),
            array("$ruleWithoutComments\nFoo", "$ruleWithoutComments\nFoo"),
            array("Foo\n\n$ruleWithoutComments\n", "Foo\n\n$ruleWithoutComments\n"),
            array("Foo\n$ruleWithoutComments\nbar\n", "Foo\n$ruleWithoutComments\nbar\n"),
            array($ruleWithComments, $ruleWithComments),
            array("$ruleWithComments\nFoo", "$ruleWithComments\nFoo"),
            array("Foo\n\n$ruleWithComments\n", "Foo\n\n$ruleWithComments\n"),
            array("Foo\n$ruleWithComments\nbar\n", "Foo\n$ruleWithComments\nbar\n"),
        );
    }
    /**
     *  @dataProvider addPrettyUrlProvider
     */
    public function testAddPrettyUrl($before, $after)
    {
        $resulting = self::$configurator->addRule($before, self::$prettyUrlRule);
        $this->assertSame($after, $resulting);
    }

    public function removePrettyUrlProvider()
    {
        self::prepareClass();
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;
        $ruleWithoutComments = self::$prettyUrlRule->getCode();
        $ruleWithComments = trim(self::$prettyUrlRule->getCommentsBefore()."\n".$ruleWithoutComments."\n".self::$prettyUrlRule->getCommentsAfter());

        return array(
            array("", ""),
            array("\n\n\n\n", "\n\n\n\n"),
            array("Foo", "Foo"),
            array("Foo\n\n\n\n", "Foo\n\n\n\n"),
            array($ruleWithoutComments, ""),
            array("$ruleWithoutComments\nFoo", "Foo\n"),
            array("Foo\n\n$ruleWithoutComments\n", "Foo\n"),
            array("Foo\n$ruleWithoutComments\nbar\n", "Foo\n\nbar\n"),
            array($ruleWithComments, ""),
            array("$ruleWithComments\nFoo", "Foo\n"),
            array("Foo\n\n$ruleWithComments\n", "Foo\n"),
            array("Foo\n$ruleWithComments\nbar\n", "Foo\n\nbar\n"),
        );
    }
    /**
     *  @dataProvider removePrettyUrlProvider
     */
    public function testRemovePrettyUrl($before, $after)
    {
        $resulting = self::$configurator->removeRule($before, self::$prettyUrlRule);
        $this->assertSame($after, $resulting);
    }
}
