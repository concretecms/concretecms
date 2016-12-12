<?php
namespace Concrete\Core\Service\Rule;

/**
 * A single rule for the web server.
 */
class Rule implements RuleInterface, ConfigurableRuleInterface
{
    /**
     * The code of the rule.
     *
     * @var string|callable
     */
    protected $code;

    /**
     * Is this rule enabled (should be present in the configuration) or disabled (should not be present in the configuration)?
     *
     * @var bool|callable
     */
    protected $enabled;

    /**
     * Optional comments to be placed before the rule itself.
     *
     * @var string
     */
    protected $commentsBefore;

    /**
     * Optional comments to be placed after the rule itself.
     *
     * @var string
     */
    protected $commentsAfter;

    /**
     * The rule options.
     *
     * @var Option[]
     */
    protected $options;

    /**
     * Intializes the instance.
     *
     * @param string|callable $code The code of the rule.
     * @param bool|callable $enabled Is this rule enabled (should be present in the configuration) or disabled (should not be present in the configuration)?
     * @param string $commentsBefore Optional comments to be placed before the rule itself.
     * @param string $commentsAfter Optional comments to be placed after the rule itself.
     */
    public function __construct($code, $enabled, $commentsBefore = '', $commentsAfter = '')
    {
        $this->code = $code;
        $this->enabled = $enabled;
        $this->commentsBefore = (string) $commentsBefore;
        $this->commentsAfter = (string) $commentsAfter;
        $this->options = array();
    }

    /**
     * {@inheritdoc}
     *
     * @see RuleInterface::getCode()
     */
    public function getCode()
    {
        $result = $this->code;
        if (is_callable($result)) {
            $result = $result($this);
        }

        return (string) $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see RuleInterface::isEnabled()
     */
    public function isEnabled()
    {
        $result = $this->enabled;
        if (is_callable($result)) {
            $result = $result($this);
        }

        return (bool) $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see RuleInterface::getCommentsBefore()
     */
    public function getCommentsBefore()
    {
        return $this->commentsBefore;
    }

    /**
     * {@inheritdoc}
     *
     * @see RuleInterface::getCommentsAfter()
     */
    public function getCommentsAfter()
    {
        return $this->commentsAfter;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Rule\ConfigurableRuleInterface::addOption()
     */
    public function addOption($handle, Option $option)
    {
        $this->options[$handle] = $option;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Rule\RuleInterface::getOptions()
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Rule\ConfigurableRuleInterface::getOption()
     */
    public function getOption($handle)
    {
        return isset($this->options[$handle]) ? $this->options[$handle] : null;
    }
}
