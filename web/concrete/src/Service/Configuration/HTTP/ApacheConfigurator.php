<?php
namespace Concrete\Core\Service\Configuration\HTTP;

use Concrete\Core\Service\Configuration\ConfiguratorInterface;
use Concrete\Core\Service\Rule\RuleInterface;

class ApacheConfigurator implements ConfiguratorInterface
{
    /**
     * Gets the rule, if present in a configuration.
     *
     * @param string $configuration The whole configuration.
     * @param RuleInterface $rule The rule to be checked.
     *
     * @return string Returns the whole rule found (or '' if not found)
     */
    protected function getConfiguredRule($configuration, RuleInterface $rule)
    {
        $configurationNormalized = str_replace(array("\r\n", "\r"), "\n", (string) $configuration);
        $rxSearch = '/';
        // First of all we have either the start of the file or a line ending
        $rxSearch .= '(^|\n)';
        $commentsBefore = $rule->getCommentsBefore();
        if ($commentsBefore !== '') {
            // Then we may have the opening comment line
            $rxSearch .= '(\s*'.preg_quote($commentsBefore, '/').'\s*\n+)?';
        }
        // Then we have the rule itself
        $rxSearch .= '\s*'.preg_replace("/\n\s*/", "\\s*\\n\\s*", preg_quote($rule->getCode(), '/')).'\s*';
        $commentsAfter = $rule->getCommentsAfter();
        if ($commentsAfter !== '') {
            // Then we may have the closing comment line
            $rxSearch .= '(\n\s*'.preg_quote($commentsAfter, '/').'\s*)?';
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
    public function hasRule($configuration, RuleInterface $rule)
    {
        return $this->getConfiguredRule($configuration, $rule) !== '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::addRule()
     */
    public function addRule($configuration, RuleInterface $rule)
    {
        if ($this->getConfiguredRule($configuration, $rule) === '') {
            $configuration = rtrim($configuration);
            if ($configuration !== '') {
                $configuration .= "\n\n";
            }
            $commentsBefore = $rule->getCommentsBefore();
            if ($commentsBefore !== '') {
                $configuration .= $commentsBefore."\n";
            }
            $configuration .= $rule->getCode()."\n";
            $commentsAfter = $rule->getCommentsAfter();
            if ($commentsAfter !== '') {
                $configuration .= $commentsAfter."\n";
            }
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Configuration\ConfiguratorInterface::removeRule()
     */
    public function removeRule($configuration, RuleInterface $rule)
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
