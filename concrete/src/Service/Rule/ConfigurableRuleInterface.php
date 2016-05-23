<?php
namespace Concrete\Core\Service\Rule;

/**
 * Interface describing a configurable rule for the web server.
 */
interface ConfigurableRuleInterface
{
    /**
     * Add an option to this rule.
     *
     * @param string $handle The option handle.
     * @param Option $option The option to add.
     */
    public function addOption($handle, Option $option);

    /**
     * Get all the rule options.
     *
     * @return Option[]
     */
    public function getOptions();

    /**
     * Get an option given its handle.
     *
     * @param string $handle The option handle.
     *
     * @return Option|null
     */
    public function getOption($handle);
}
