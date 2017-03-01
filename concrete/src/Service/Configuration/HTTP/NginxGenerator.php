<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Rule\Option as RuleOption;
use Concrete\Core\Service\Rule\Rule;
use Concrete\Core\Service\Rule\RuleInterface;
use Exception;

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

        $rule = new Rule(
            function (Rule $rule) {
                $DIR_REL = $rule->getOption('dir_rel')->getValue();
                if ($DIR_REL === null) {
                    if (\Core::make('app')->isRunThroughCommandLineInterface()) {
                        throw new Exception(t('When executed from the command line, you need to specify the %s option', 'dir_rel'));
                    } else {
                        $DIR_REL = DIR_REL;
                    }
                }
                $DIR_REL = trim((string) $DIR_REL, '/');
                if ($DIR_REL !== '') {
                    $DIR_REL = '/' . $DIR_REL;
                }
                $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

                return <<<EOT
location {$DIR_REL}/ {
	try_files \$uri \$uri/ {$DIR_REL}/{$DISPATCHER_FILENAME}?\$query_string;
}
EOT
                ;
            },
            function () {
                return (bool) \Config::get('concrete.seo.url_rewriting');
            },
            '# -- concrete5 urls start --',
            '# -- concrete5 urls end --'
        );

        $option = new RuleOption(
            t('concrete5 path relative to website root'),
            function () {
                return \Core::make('app')->isRunThroughCommandLineInterface();
            }
        );

        $rule->addOption('dir_rel', $option);

        return $rule;
    }
}
