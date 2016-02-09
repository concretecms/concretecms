<?php
namespace Concrete\Core\Service\Rule;

class Rule implements RuleInterface
{
    /**
     * The code of the rule.
     *
     * @var string
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
     * Intializes the instance.
     *
     * @param string $code The code of the rule.
     * @param bool|callable $enabled Is this rule enabled (should be present in the configuration) or disabled (should not be present in the configuration)?
     * @param string $commentsBefore Optional comments to be placed before the rule itself.
     * @param string $commentsAfter Optional comments to be placed after the rule itself.
     */
    public function __construct($code, $enabled, $commentsBefore = '', $commentsAfter = '')
    {
        $this->code = (string) $code;
        $this->enabled = $enabled;
        $this->commentsBefore = (string) $commentsBefore;
        $this->commentsAfter = (string) $commentsAfter;
    }

    /**
     * {@inheritdoc}
     *
     * @see RuleInterface::getCode()
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     *
     * @see RuleInterface::isEnabled()
     */
    public function isEnabled()
    {
        $result = $this->enabled;
        if (is_callable($this->enabled)) {
            $result = (bool) $result($this);
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
}
