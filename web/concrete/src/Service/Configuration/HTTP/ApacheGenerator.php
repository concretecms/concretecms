<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Application\Application;

class ApacheGenerator implements GeneratorInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $enabledRules;

    /**
     * Initializes the instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {

        $this->app = $app;
        $this->rules = array();
        $this->enabledRules = array();
        $this->addRule(
            'pretty_urls',
            $this->getPrettyUrlRule(),
            function (Application $app) {
                return (bool) $app->make('config')->get('concrete.seo.url_rewriting');
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\GeneratorInterface::addRule()
     */
    public function addRule($handle, $rule, $enabled)
    {
        $this->rules[$handle] = $rule;
        $this->enabledRules[$handle] = $enabled;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\GeneratorInterface::getRules()
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\GeneratorInterface::getRule()
     */
    public function getRule($handle)
    {
        $rules = $this->getRules();

        return isset($rules[$handle]) ? $rules[$handle] : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\GeneratorInterface::ruleShouldBeEnabled()
     */
    public function ruleShouldBeEnabled($handle)
    {
        $result = null;
        if (isset($this->enabledRules[$handle])) {
            $enabled = $this->enabledRules[$handle];
            if (is_callable($enabled)) {
                $enabled = $enabled($this->app, $this);
            }
            $result = (bool) $enabled;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getPrettyUrlRule()
    {
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

        return array(
            'commentBefore' => '# -- concrete5 urls start --',
            'code' => <<<EOT
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase $DIR_REL/
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME}/index.html !-f
	RewriteCond %{REQUEST_FILENAME}/index.php !-f
	RewriteRule . $DISPATCHER_FILENAME [L]
</IfModule>
EOT
            ,
            'commentAfter' => "# -- concrete5 urls end --",
        );
    }
}
