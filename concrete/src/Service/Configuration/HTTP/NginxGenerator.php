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
                'location ^~ /application/files/site-specific/ { deny all; return 404; }',
                '',
                '# Validate the Host header before using it as a filesystem path.',
                '# $ccm_site_dir is empty for invalid hostnames, so try_files falls through to the',
                '# webroot file instead of resolving a traversal path like "site-specific/../...".',
                'set $ccm_site_dir "";',
                'if ($host ~* ^[a-z0-9][a-z0-9.\-]*[a-z0-9]$) { set $ccm_site_dir /application/files/site-specific/$host; }',
                '',
                'location = /robots.txt               { try_files $ccm_site_dir/robots.txt   /robots.txt               =404; }',
                'location = /sitemap.xml              { try_files $ccm_site_dir/sitemap.xml  /sitemap.xml              =404; }',
                'location = /ads.txt                  { try_files $ccm_site_dir/ads.txt      /ads.txt                  =404; }',
                'location = /humans.txt               { try_files $ccm_site_dir/humans.txt   /humans.txt               =404; }',
                'location = /llms.txt                 { try_files $ccm_site_dir/llms.txt     /llms.txt                 =404; }',
                '# security.txt is stored flat but served at /.well-known/security.txt (RFC 9116).',
                'location = /.well-known/security.txt { try_files $ccm_site_dir/security.txt /.well-known/security.txt =404; }',
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
	try_files \$uri \$uri/index.html \$uri/index.php {$DIR_REL}/{$DISPATCHER_FILENAME}?\$query_string;
}
EOT
                ;
            },
            function () {
                return (bool) \Config::get('concrete.seo.url_rewriting');
            },
            '# -- concrete urls start --',
            '# -- concrete urls end --'
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
