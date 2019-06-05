<?php

namespace Concrete\Core\Package\Offline\Parser;

use Concrete\Core\Package\Offline\Parser;

/**
 * The token parser for legacy (prior to concrete5 5.7) packages.
 */
class Legacy extends Parser
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::canParseTokens()
     */
    public function canParseTokens(array $tokens)
    {
        // Pre 5.7 packages don't use namespaces
        return $this->findTypedToken($tokens, [T_NAMESPACE]) === null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::getControllerClassNameRegularExpression()
     */
    protected function getControllerClassNameRegularExpression()
    {
        return '/^\w+Package$/';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::getPackageHandleFromClassName()
     */
    protected function getPackageHandleFromClassName(array $tokens, $className, $classStart)
    {
        $matches = null;
        preg_match('/^(.+)Package$/', $className, $matches);

        return uncamelcase($matches[1]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::getDefaultPackageMinimumCodeVersion()
     */
    protected function getDefaultPackageMinimumCodeVersion()
    {
        return '5.0.0'; // https://github.com/concrete5/concrete5-legacy/blob/5.6.4.0/web/concrete/core/models/package.php#L144
    }
}
