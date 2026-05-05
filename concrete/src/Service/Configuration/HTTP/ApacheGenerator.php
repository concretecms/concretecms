<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Rule\Rule;
use Concrete\Core\Service\Rule\RuleInterface;
use Concrete\Core\Service\Rule\Option as RuleOption;
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
        $this->addRule('well_known_files', $this->getWellKnownRule());
    }

    /**
     * @return RuleInterface
     */
    protected function getWellKnownRule()
    {
        return new Rule(
            implode("\n", [
                '# Block direct HTTP access to the per-site storage directory.',
                'RewriteRule ^application/files/site-specific/ - [F,L]',
                '',
                '# security.txt is stored flat but served at /.well-known/security.txt (RFC 9116).',
                '# Validate hostname before using it in a filesystem path to prevent path traversal via Host headers.',
                'RewriteCond %{HTTP_HOST} ^([a-zA-Z0-9][a-zA-Z0-9.\-]*[a-zA-Z0-9])(:[0-9]+)?$',
                'RewriteCond %{DOCUMENT_ROOT}/application/files/site-specific/%1/security.txt -f',
                'RewriteRule ^\.well-known/security\.txt$ /application/files/site-specific/%1/security.txt [L]',
                '',
                '# Route root-level well-known files to per-site overrides when available.',
                'RewriteCond %{HTTP_HOST} ^([a-zA-Z0-9][a-zA-Z0-9.\-]*[a-zA-Z0-9])(:[0-9]+)?$',
                'RewriteCond %{DOCUMENT_ROOT}/application/files/site-specific/%1/%{REQUEST_URI} -f',
                'RewriteRule ^(robots\.txt|sitemap\.xml|ads\.txt|humans\.txt|llms\.txt)$ /application/files/site-specific/%1/$1 [L]',
            ]),
            true,
            '# -- concrete well-known start --',
            '# -- concrete well-known end --'
        );
    }

    /**
     * @return RuleInterface
     */
    protected function getPrettyUrlRule()
    {
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
                    $DIR_REL = '/'.$DIR_REL;
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
            "# -- concrete urls start --",
            "# -- concrete urls end --"
        );

        $option = new RuleOption(
            t('Concrete path relative to website root'),
            function () {
                return \Core::make('app')->isRunThroughCommandLineInterface();
            }
        );

        $rule->addOption('dir_rel', $option);

        return $rule;
    }
}
