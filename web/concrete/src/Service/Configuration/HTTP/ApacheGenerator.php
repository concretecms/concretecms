<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Rule\Rule;
use Concrete\Core\Service\Rule\RuleInterface;

class ApacheGenerator implements GeneratorInterface
{
    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * Initializes the instance.
     */
    public function __construct()
    {
        $this->rules = array();
        $this->addRule('pretty_urls', $this->getPrettyUrlRule());
    }

    /**
     * {@inheritdoc}
     *
     * @see GeneratorInterface::addRule()
     */
    public function addRule($handle, RuleInterface $rule)
    {
        $this->rules[$handle] = $rule;
    }

    /**
     * {@inheritdoc}
     *
     * @see GeneratorInterface::getRules()
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     *
     * @see GeneratorInterface::getRule()
     */
    public function getRule($handle)
    {
        $rules = $this->getRules();

        return isset($rules[$handle]) ? $rules[$handle] : null;
    }

    /**
     * @return RuleInterface
     */
    protected function getPrettyUrlRule()
    {
        $DIR_REL = DIR_REL;
        $DISPATCHER_FILENAME = DISPATCHER_FILENAME;

        return new Rule(
            <<<EOT
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
            function () {
                return (bool) \Config::get('concrete.seo.url_rewriting');
            },
            '# -- concrete5 urls start --',
            "# -- concrete5 urls end --"
        );
    }
}
