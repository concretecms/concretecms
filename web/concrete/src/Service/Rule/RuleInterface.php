<?php
namespace Concrete\Core\Service\Rule;

/**
 * Interface describing a single rule for the web server.
 */
interface RuleInterface
{
    /**
     * Return the code of the rule.
     *
     * @return string
     */
    public function getCode();

    /**
     * Is this rule enabled (should be present in the configuration) or disabled (should not be present in the configuration)?
     */
    public function isEnabled();

    /**
     * Return optional comments to be placed before the rule itself.
     *
     * @return string
     */
    public function getCommentsBefore();

    /**
     * Return optional comments to be placed after the rule itself.
     *
     * @return string
     */
    public function getCommentsAfter();
}
