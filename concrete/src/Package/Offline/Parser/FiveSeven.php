<?php

namespace Concrete\Core\Package\Offline\Parser;

use Concrete\Core\Package\Offline\Exception;
use Concrete\Core\Package\Offline\Parser;

/**
 * The token parser for concrete5 5.7+ packages.
 */
class FiveSeven extends Parser
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::canParseTokens()
     */
    public function canParseTokens(array $tokens)
    {
        // 5.7+ packages use PHP namespaces
        $namespaceIndex = $this->findTypedToken($tokens, [T_NAMESPACE]);
        if ($namespaceIndex === null) {
            return false;
        }
        // Multiple namespaces are not supported
        return $this->findTypedToken($tokens, [T_NAMESPACE], $namespaceIndex + 1) === null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::getControllerClassNameRegularExpression()
     */
    protected function getControllerClassNameRegularExpression()
    {
        return '/^Controller$/';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::getPackageHandleFromClassName()
     */
    protected function getPackageHandleFromClassName(array $tokens, $className, $classStart)
    {
        $namespaceIndex = $this->findTypedToken($tokens, [T_NAMESPACE], 0, $classStart - 1);
        // PHP 8 changed namespace tokens
        if (\PHP_MAJOR_VERSION > 7) {
            $namespaceNameIndex = $this->findTypedToken($tokens, [T_NAME_QUALIFIED], $namespaceIndex + 1, $classStart - 1);
        } else {
            $namespaceNameIndex = $this->findTypedToken($tokens, [T_STRING, T_NS_SEPARATOR], $namespaceIndex + 1, $classStart - 1);
        }

        if ($namespaceNameIndex === null) {
            throw Exception::create(Exception::ERRORCODE_MISSING_NAMESPACENAME, t('Unable to find the namespace name'));
        }
        $namespaceName = '';
        if (\PHP_MAJOR_VERSION < 8) {
            while (is_array($tokens[$namespaceNameIndex]) && in_array($tokens[$namespaceNameIndex][0], [T_STRING, T_NS_SEPARATOR])) {
                $namespaceName .= $tokens[$namespaceNameIndex][1];
                ++$namespaceNameIndex;
            }
            $namespaceName = trim($namespaceName, '\\');
        } else {
            $namespaceName = $tokens[$namespaceNameIndex][1];
        }

        $matches = null;
        if (!preg_match('/^Concrete\\\\Package\\\\(\w+)$/', $namespaceName, $matches)) {
            throw Exception::create(Exception::ERRORCODE_INVALID_NAMESPACENAME, t('The namespace "%s" is not valid', $namespaceName));
        }

        return uncamelcase($matches[1]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Offline\Parser::getDefaultPackageMinimumCodeVersion()
     */
    protected function getDefaultPackageMinimumCodeVersion()
    {
        return '5.7.0'; // https://github.com/concrete5/concrete5/blob/8.5.1/concrete/src/Package/Package.php#L183
    }
}
