<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;

class NginxGenerator implements GeneratorInterface
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
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;
        $this->app = $app;
        $this->rules = array();
        $this->enabledRules = array();
        $this->addRule(
            'pretty_urls',
            array(
                'commentsBefore' => "# -- concrete5 urls start --",
                'code' => <<<EOT
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
		rewrite ^/(.*)$ /index.php/$1 last;
	}
}
EOT
                ,
                'commentsAfter' => "# -- concrete5 urls end --",
            ),
            function (Application $app) {
                return (bool) $app->make('config')->get('concrete.seo.url_rewriting');
            }
        );
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
}
