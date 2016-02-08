<?php
namespace Concrete\Core\Service\Configuration;

interface ConfiguratorInterface
{
    /**
     * Checks if a configuration contains a rule.
     *
     * @param string $configuration The whole configuration.
     * @param array $rule {
     *
     *     @var string $commentBefore [optional] An optional part that *may* be present in the configuration before the rule to be checked.
     *     @var string $code The code of the rule
     *     @var string $commentAfter [optional] An optional part that *may* be present in the configuration after the rule to be checked.
     * }
     *
     * @return bool
     */
    public function hasRule($configuration, array $rule);

    /**
     * Adds a rule to the configuration (if not already there).
     *
     * @param string $configuration The whole configuration.
     * @param array $rule {
     *
     *     @var string $commentBefore [optional] An optional part that *may* be present in the configuration before the rule to be checked.
     *     @var string $code The code of the rule
     *     @var string $commentAfter [optional] An optional part that *may* be present in the configuration after the rule to be checked.
     * }
     *
     * @return string Returns the modified configuration.
     */
    public function addRule($configuration, array $rule);

    /**
     * Removes a rule from the configuration (if it's there).
     *
     * @param string $configuration The whole configuration.
     * @param array $rule {
     *
     *     @var string $commentBefore [optional] An optional part that *may* be present in the configuration before the rule to be checked.
     *     @var string $code The code of the rule
     *     @var string $commentAfter [optional] An optional part that *may* be present in the configuration after the rule to be checked.
     * }
     *
     * @return string Returns the modified configuration.
     */
    public function removeRule($configuration, array $rule);
}
