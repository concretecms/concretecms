<?php

namespace Concrete\Tests\Service;

use PHPUnit_Framework_TestCase;

class ApacheRulesTest extends PHPUnit_Framework_TestCase
{
    private static $prepared = false;
    /**
     * @var \Concrete\Core\Service\Configuration\HTTP\ApacheConfigurator
     */
    private static $configurator;

    /**
     * @var \Concrete\Core\Service\Rule\Rule;
     */
    private static $prettyUrlRule;

    public function detectPrettyUrlProvider()
    {
        self::prepareClass();
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

        return [
            [false, ''],
            [true, <<<EOT
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase $DIR_REL/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}/index.html !-f
RewriteCond %{REQUEST_FILENAME}/index.php !-f
RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
            ],
            [true, <<<EOT
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase $DIR_REL/
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME}/index.html !-f
	RewriteCond %{REQUEST_FILENAME}/index.php !-f
	RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
            ],
            [true, <<<EOT
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
            ],
            [true, <<<EOT
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
            ],
            [true, <<<EOT
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
            ],
            [true, <<<EOT
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
            ],
        ];
    }

    /**
     *  @dataProvider detectPrettyUrlProvider
     *
     * @param mixed $found
     * @param mixed $htaccess
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
        $ruleWithComments = trim(self::$prettyUrlRule->getCommentsBefore() . "\n" . $ruleWithoutComments . "\n" . self::$prettyUrlRule->getCommentsAfter());

        return [
            ['', "$ruleWithComments\n"],
            ["\n\n\n\n", "$ruleWithComments\n"],
            ['Foo', "Foo\n\n$ruleWithComments\n"],
            ["Foo\n\n\n\n", "Foo\n\n$ruleWithComments\n"],
            [$ruleWithoutComments, $ruleWithoutComments],
            ["$ruleWithoutComments\nFoo", "$ruleWithoutComments\nFoo"],
            ["Foo\n\n$ruleWithoutComments\n", "Foo\n\n$ruleWithoutComments\n"],
            ["Foo\n$ruleWithoutComments\nbar\n", "Foo\n$ruleWithoutComments\nbar\n"],
            [$ruleWithComments, $ruleWithComments],
            ["$ruleWithComments\nFoo", "$ruleWithComments\nFoo"],
            ["Foo\n\n$ruleWithComments\n", "Foo\n\n$ruleWithComments\n"],
            ["Foo\n$ruleWithComments\nbar\n", "Foo\n$ruleWithComments\nbar\n"],
        ];
    }

    /**
     *  @dataProvider addPrettyUrlProvider
     *
     * @param mixed $before
     * @param mixed $after
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
        $ruleWithComments = trim(self::$prettyUrlRule->getCommentsBefore() . "\n" . $ruleWithoutComments . "\n" . self::$prettyUrlRule->getCommentsAfter());

        return [
            ['', ''],
            ["\n\n\n\n", "\n\n\n\n"],
            ['Foo', 'Foo'],
            ["Foo\n\n\n\n", "Foo\n\n\n\n"],
            [$ruleWithoutComments, ''],
            ["$ruleWithoutComments\nFoo", "Foo\n"],
            ["Foo\n\n$ruleWithoutComments\n", "Foo\n"],
            ["Foo\n$ruleWithoutComments\nbar\n", "Foo\n\nbar\n"],
            [$ruleWithComments, ''],
            ["$ruleWithComments\nFoo", "Foo\n"],
            ["Foo\n\n$ruleWithComments\n", "Foo\n"],
            ["Foo\n$ruleWithComments\nbar\n", "Foo\n\nbar\n"],
        ];
    }

    /**
     *  @dataProvider removePrettyUrlProvider
     *
     * @param mixed $before
     * @param mixed $after
     */
    public function testRemovePrettyUrl($before, $after)
    {
        $resulting = self::$configurator->removeRule($before, self::$prettyUrlRule);
        $this->assertSame($after, $resulting);
    }

    private static function prepareClass()
    {
        if (!self::$prepared) {
            $apache = \Core::make('\Concrete\Core\Service\HTTP\Apache', ['']);
            /* @var $apache \Concrete\Core\Service\HTTP\Apache */
            self::$configurator = $apache->getConfigurator();
            self::$prettyUrlRule = $apache->getGenerator()->getRule('pretty_urls');
            self::$prettyUrlRule->getOption('dir_rel')->setValue(DIR_REL);
            self::$prepared = true;
        }
    }
}
