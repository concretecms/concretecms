<?php
namespace Concrete\Core\Service\Configuration;

interface GeneratorInterface
{
    /**
     * Add a new rule to be handled.
     *
     * @param string $handle The rule handler
     * @param string $rule The rule
     * @param bool|callable|null $enabled Should the rule enabled (true) or disabled (false)?
     */
    public function addRule($handle, $rule, $enabled);

    /**
     * Returns all the defined rules. The keys are the rules handles, the values are the rule definitions.
     * Each definition is an array with the following keys:
     * - string 'commentBefore' [optional] An optional part that *may* be present in the configuration before the rule to be checked.
     * - string 'code' The code of the rule
     * - string 'commentAfter' [optional] An optional part that *may* be present in the configuration after the rule to be checked.
     *
     * @return array[]
     */
    public function getRules();

    /**
     * Return a defined rule given its handle (if found).
     *
     * @param string $handle
     *
     * @return null|array {
     *
     *     @var string $commentBefore [optional] An optional part that *may* be present in the configuration before the rule to be checked.
     *     @var string $code The code of the rule
     *     @var string $commentAfter [optional] An optional part that *may* be present in the configuration after the rule to be checked.
     * }
     */
    public function getRule($handle);

    /**
     * Check is a rule should be enabled or disabled.
     *
     * @param string $handle The rule handle
     *
     * @return bool|null Return true if the rule should be enabled, false if it should be disabled, null if we don't know.
     */
    public function ruleShouldBeEnabled($handle);
}
