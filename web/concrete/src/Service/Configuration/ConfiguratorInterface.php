<?php
namespace Concrete\Core\Service\Configuration;

use Concrete\Core\Service\Rule\RuleInterface;

interface ConfiguratorInterface
{
    /**
     * Checks if a configuration contains a rule.
     *
     * @param string $configuration The whole configuration.
     * @param RuleInterface $rule The rule to be checked.
     *
     * @return bool
     */
    public function hasRule($configuration, RuleInterface $rule);

    /**
     * Adds a rule to the configuration (if not already there).
     *
     * @param string $configuration The whole configuration.
     * @param RuleInterface $rule The rule to be added to the configuration.
     *
     * @return string Returns the modified configuration.
     */
    public function addRule($configuration, RuleInterface $rule);

    /**
     * Removes a rule from the configuration (if it's there).
     *
     * @param string $configuration The whole configuration.
     * @param RuleInterface $rule The rule to be removed from the configuration.
     *
     * @return string Returns the modified configuration.
     */
    public function removeRule($configuration, RuleInterface $rule);
}
