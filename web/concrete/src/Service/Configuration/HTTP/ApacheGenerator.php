<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Rule\Rule;
use Concrete\Core\Service\Rule\RuleInterface;
use Exception;

class ApacheGenerator extends Generator implements GeneratorInterface
{
    /**
     * Initializes the instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->addRule('pretty_urls', $this->getPrettyUrlRule());
    }

    /**
     * @return RuleInterface
     */
    protected function getPrettyUrlRule()
    {
        return new Rule(
            function (RuleInterface $rule) {
                $options = $rule->getOptions();
                $DIR_REL = null;
                if (isset($options['DIR_REL'])) {
                    $DIR_REL = trim($options['DIR_REL'], '/');
                    if ($DIR_REL !== '') {
                        $DIR_REL = '/'.$DIR_REL;
                    }
                }
                if ($DIR_REL === null) {
                    if (\Core::make('app')->isRunThroughCommandLineInterface()) {
                        throw new Exception(t('When executed from the command line, you need to specify the %s option', 'DIR_REL'));
                    } else {
                        $DIR_REL = DIR_REL;
                    }
                }
                $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

                return <<<EOT
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase $DIR_REL/
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME}/index.html !-f
	RewriteCond %{REQUEST_FILENAME}/index.php !-f
	RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
                ;
            },
            function () {
                return (bool) \Config::get('concrete.seo.url_rewriting');
            },
            "# -- concrete5 urls start --",
            "# -- concrete5 urls end --"
        );
    }
}
