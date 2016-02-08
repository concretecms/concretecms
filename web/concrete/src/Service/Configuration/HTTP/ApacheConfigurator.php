<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\ConfiguratorInterface;

class ApacheConfigurator implements ConfiguratorInterface
{
    /**
     * Gets the rule, if present in a configuration.
     *
     * @param string $configuration The whole configuration.
     * @param array $rule {
     *
     *     @var string $commentBefore [optional] An optional part that *may* be present in the configuration before the rule to be checked.
     *     @var string $code The code of the rule
     *     @var string $commentAfter [optional] An optional part that *may* be present in the configuration after the rule to be checked.
     * }
     *
     * @return string Returns the whole rule found (or '' if not found)
     */
    protected function getConfiguredRule($configuration, array $rule)
    {
        $configurationNormalized = str_replace(array("\r\n", "\r"), "\n", (string) $configuration);
        $rxSearch = '/';
        // First of all we have either the start of the file or a line ending
        $rxSearch .= '(^|\n)';
        if (isset($rule['commentBefore'])) {
            // Then we may have the opening comment line
            $rxSearch .= '(\s*'.preg_quote($rule['commentBefore'], '/').'\s*\n+)?';
        }
        // Then we have the rule itself
        $rxSearch .= '\s*'.preg_replace("/\n\s*/", "\\s*\\n\\s*", preg_quote($rule['code'], '/')).'\s*';
        if (isset($rule['commentAfter'])) {
            // Then we may have the closing comment line
            $rxSearch .= '(\n\s*'.preg_quote($rule['commentAfter'], '/').'\s*)?';
        }
        // Finally we have the end of the file or a line ending
        $rxSearch .= '(\n|$)';
        $rxSearch .= '/';

        return preg_match($rxSearch, $configurationNormalized, $match) ? $match[0] : '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::hasRule()
     */
    public function hasRule($configuration, array $rule)
    {
        return $this->getConfiguredRule($configuration, $rule) !== '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::addRule()
     */
    public function addRule($configuration, array $rule)
    {
        if ($this->getConfiguredRule($configuration, $rule) === '') {
            $configuration = rtrim($configuration);
            if ($configuration !== '') {
                $configuration .= "\n\n";
            }
            if (isset($rule['commentBefore'])) {
                $configuration .= $rule['commentBefore']."\n";
            }
            $configuration .= $rule['code']."\n";
            if (isset($rule['commentAfter'])) {
                $configuration .= $rule['commentAfter']."\n";
            }
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::removeRule()
     */
    public function removeRule($configuration, array $rule)
    {
        $current = $this->getConfiguredRule($configuration, $rule);
        if ($current !== '') {
            $configuration = str_replace(array("\r\n", "\r"), "\n", (string) $configuration);
            $configuration = trim(str_replace($current, "\n\n", $configuration));
            if ($configuration !== '') {
                $configuration .= "\n";
            }
        }

        return $configuration;
    }
}
