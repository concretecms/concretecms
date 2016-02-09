<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Rule\Rule;
use Concrete\Core\Service\Rule\RuleInterface;

class NginxGenerator extends Generator implements GeneratorInterface
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
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

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
location $DIR_REL/ {
	set \$do_rewrite 1
	if (-f \$request_filename) {
		set \$do_rewrite 0
	)
	if (-f \$request_filename/index.html) {
		set \$do_rewrite 0
	)
	if (-f \$request_filename/index.php) {
		set \$do_rewrite 0
	)
	if (-d \$request_filename) {
		set \$do_rewrite 0
	)
	if (\$do_rewrite = "1") {
		rewrite ^/(.*)$ /$DISPATCHER_FILENAME/$1 last;
	}
}
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
