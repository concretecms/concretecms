<?php
namespace Concrete\Core\Service\Configuration;

use Concrete\Core\Service\Rule\RuleInterface;

interface GeneratorInterface
{
    /**
     * Add a new rule to be handled.
     *
     * @param string $handle The rule handler
     * @param RuleInterface $rule The rule to be handled
     */
    public function addRule($handle, RuleInterface $rule);

    /**
     * Returns all the defined rules.
     *
     * @return RuleInterface[]
     */
    public function getRules();

    /**
     * Return a defined rule given its handle (if found).
     *
     * @param string $handle
     *
     * @return null|RuleInterface
     */
    public function getRule($handle);
}
