<?php

namespace Concrete\Core\Package\Offline;

use Concrete\Core\Utility\Service\Validation\Strings as StringValidator;

/**
 * Base class for actual parsers that extract informations from package controller files.
 */
abstract class Parser
{
    /**
     * The string validator service to be used to check the validity of handles.
     *
     * @var \Concrete\Core\Utility\Service\Validation\Strings
     */
    protected $stringValidator;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Utility\Service\Validation\Strings $stringValidator the string validator service to be used to check the validity of handles
     */
    public function __construct(StringValidator $stringValidator)
    {
        $this->stringValidator = $stringValidator;
    }

    /**
     * Determine if this parser can be used for the specified PHP tokens of the package controlller.php file.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     *
     * @return bool
     */
    abstract public function canParseTokens(array $tokens);

    /**
     * Extract package details from the PHP tokens of the package controller.php file.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    public function extractInfo(array $tokens)
    {
        list($className, $classStart, $classEnd) = $this->findControllerClass($tokens);

        return PackageInfo::create()
            ->setHandle($this->getPackageHandle($tokens, $className, $classStart, $classEnd))
            ->setVersion($this->getPackageVersion($tokens, $className, $classStart, $classEnd))
            ->setName($this->getPackageName($tokens, $className, $classStart, $classEnd))
            ->setDescription($this->getPackageDescription($tokens, $className, $classStart, $classEnd))
            ->setMinimumCoreVersion($this->getPackageMinimumCodeVersion($tokens, $className, $classStart, $classEnd))
        ;
    }

    /**
     * Get the regular expression that the controller class name must match (without the namespace).
     *
     * @return string
     */
    abstract protected function getControllerClassNameRegularExpression();

    /**
     * Get the default minimum core version when the package doesn't specify it.
     *
     * @return string
     */
    abstract protected function getDefaultPackageMinimumCodeVersion();

    /**
     * Extract the package handle from the fully-qualified class name.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $className the  class name of the package controller
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    abstract protected function getPackageHandleFromClassName(array $tokens, $className, $classStart);

    /**
     * Find the package controller class.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return array[] [$className, $classStart, $classEnd] returns the class name, the index of the first PHP token of the class body (its first '{'}, and the index of the last PHP token of the class body (its last '}'}
     */
    protected function findControllerClass(array $tokens)
    {
        $result = null;
        $numTokens = count($tokens);
        for ($tokenIndex = 0; $tokenIndex < $numTokens;) {
            $classIndex = $this->findTypedToken($tokens, [T_CLASS], $tokenIndex, $numTokens);
            if ($classIndex === null) {
                break;
            }
            $classStart = $this->findTextToken($tokens, ['{'], $classIndex + 1, $numTokens);
            if ($classStart === null) {
                throw Exception::create(Exception::ERRORCODE_MISSING_OPENCURLY, t('Unable to find the opening of the controller class'));
            }
            $tokenIndex = $classStart;
            $nestingLevel = 1;
            for (; ;) {
                $tokenIndex = $this->findTextToken($tokens, ['{', '}'], $tokenIndex + 1, $numTokens);
                if ($tokenIndex === null) {
                    throw Exception::create(Exception::ERRORCODE_MISSING_CLOSECURLY, t('Unable to find the closing of the controller class'));
                }
                if ($tokens[$tokenIndex] === '{') {
                    ++$nestingLevel;
                } else {
                    --$nestingLevel;
                    if ($nestingLevel === 0) {
                        $classEnd = $tokenIndex;
                        ++$tokenIndex;
                        break;
                    }
                }
            }
            $classNameIndex = $this->findTypedToken($tokens, [T_STRING], $classIndex, $classStart);
            if ($classNameIndex === null) {
                throw Exception::create(Exception::ERRORCODE_MISSING_CLASSNAME, t('Unable to find the name of the controller class'));
            }
            $className = $tokens[$classNameIndex][1];
            if (preg_match($this->getControllerClassNameRegularExpression(), $className)) {
                if ($result !== null) {
                    throw Exception::create(Exception::ERRORCODE_MULTIPLE_CONTROLLECLASSES, t('Multiple controller classes found'));
                }
                $result = [$className, $classStart, $classEnd];
            }
        }
        if ($result === null) {
            throw Exception::create(Exception::ERRORCODE_CONTROLLERCLASS_NOT_FOUND, t('Unable to find the controller class'));
        }

        return $result;
    }

    /**
     * Get the package handle.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $className the  class name of the package controller
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return string
     */
    protected function getPackageHandle(array $tokens, $className, $classStart, $classEnd)
    {
        $fromClassName = $this->getPackageHandleFromClassName($tokens, $className, $classStart);
        $fromProperty = $this->getPropertyValue($tokens, '$pkgHandle', $classStart, $classEnd);
        if (!is_string($fromProperty)) {
            throw Exception::create(Exception::ERRORCODE_MISSING_PACKAGEHANDLE_PROPERTY, t('The package controller lacks the %s property', '$pkgHandle'));
        }
        if ($fromClassName !== $fromProperty) {
            throw Exception::create(Exception::ERRORCODE_MISMATCH_PACKAGEHANDLE, t('The package handle defined by the class name (%1$s) differs from the one defined by the %2$s property (%3$s)', $fromClassName, '$pkgHandle', $fromProperty));
        }
        if (!$this->stringValidator->handle($fromClassName)) {
            throw Exception::create(Exception::ERRORCODE_INVALID_PACKAGEHANDLE, t('The package handle "%s" is not valid', $fromClassName));
        }

        return $fromClassName;
    }

    /**
     * Get the package version.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $className the  class name of the package controller
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return string
     */
    protected function getPackageVersion(array $tokens, $className, $classStart, $classEnd)
    {
        $fromProperty = (string) $this->getPropertyValue($tokens, '$pkgVersion', $classStart, $classEnd);
        if ($fromProperty === '') {
            throw Exception::create(Exception::ERRORCODE_MISSING_PACKAGEVERSION_PROPERTY, t('The package controller lacks the %s property', '$pkgVersion'));
        }
        if (!$this->isVersionValid($fromProperty)) {
            throw Exception::create(Exception::ERRORCODE_INVALID_PACKAGEVERSION, t('The package version "%s" is not valid', $fromProperty));
        }

        return $fromProperty;
    }

    /**
     * Get the package name.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $className the  class name of the package controller
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return string
     */
    protected function getPackageName(array $tokens, $className, $classStart, $classEnd)
    {
        $fromMethod = (string) $this->getSimpleMethodReturnValue($tokens, 'getPackageName', $classStart, $classEnd);
        if ($fromMethod !== '') {
            return $fromMethod;
        }
        $fromProperty = (string) $this->getPropertyValue($tokens, '$pkgName', $classStart, $classEnd);
        if ($fromProperty !== '') {
            return $fromProperty;
        }
        throw Exception::create(Exception::ERRORCODE_MISSING_PACKAGENAME, t('The package controller lacks both the %1$s property and the %2$s method', '$pkgName', 'getPackageName'));
    }

    /**
     * Get the package description.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $className the  class name of the package controller
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return string
     */
    protected function getPackageDescription(array $tokens, $className, $classStart, $classEnd)
    {
        $fromMethod = (string) $this->getSimpleMethodReturnValue($tokens, 'getPackageDescription', $classStart, $classEnd);
        if ($fromMethod !== '') {
            return $fromMethod;
        }
        $fromProperty = (string) $this->getPropertyValue($tokens, '$pkgDescription', $classStart, $classEnd);
        if ($fromProperty !== '') {
            return $fromProperty;
        }

        return '';
    }

    /**
     * Get the minimum supported core version version.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $className the  class name of the package controller
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return string
     */
    protected function getPackageMinimumCodeVersion(array $tokens, $className, $classStart, $classEnd)
    {
        $fromProperty = $this->getPropertyValue($tokens, '$appVersionRequired', $classStart, $classEnd);

        return (string) $fromProperty === '' ? $this->getDefaultPackageMinimumCodeVersion() : $fromProperty;
    }

    /**
     * Get the value of a class property.
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $propertyName
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return mixed
     */
    protected function getPropertyValue(array $tokens, $propertyName, $classStart, $classEnd)
    {
        $nestingLevel = 0;
        for ($index = $classStart + 1; $index < $classEnd; ++$index) {
            $token = $tokens[$index];
            if ($token === '{') {
                ++$nestingLevel;
            } elseif ($token === '}') {
                --$nestingLevel;
            } elseif ($nestingLevel === 0 && is_array($token) && $token[0] === T_VARIABLE && $token[1] === $propertyName) {
                $semicolonIndex = $this->findTextToken($tokens, [';'], $index + 1, $classEnd);
                if ($semicolonIndex === null) {
                    throw Exception::create(Exception::ERRORCODE_MISSIMG_SEMICOLON, t('Missing semicolon after property %s', $propertyName));
                }
                $equalsIndex = $this->findTextToken($tokens, ['='], $index + 1, $semicolonIndex);
                if ($equalsIndex === null) {
                    return null;
                }
                $valueTokens = $this->stripNonCodeTokens($tokens, $equalsIndex + 1, $semicolonIndex);
                if (count($valueTokens) !== 1) {
                    throw Exception::create(Exception::ERRORCODE_UNSUPPORTED_PROPERTYVALUE, t('Decoding complex tokens for property %s is not supported', $propertyName));
                }
                if (!is_array($valueTokens[0])) {
                    throw Exception::create(Exception::ERRORCODE_UNSUPPORTED_PROPERTYVALUE, t('Decoding string tokens for property %s is not supported', $propertyName));
                }

                return $this->decodeValueToken($valueTokens, $propertyName);
            }
        }

        return null;
    }

    /**
     * Get the return value of a class method.
     * Get the value returned from a very simple class method (that is, containing only a "return something;").
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $methodName
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return mixed
     */
    protected function getSimpleMethodReturnValue(array $tokens, $methodName, $classStart, $classEnd)
    {
        $range = $this->getSimpleMethodBodyTokenRange($tokens, $methodName, $classStart, $classEnd);
        if ($range === null) {
            return null;
        }
        $codeTokens = $this->stripNonCodeTokens($tokens, $range[0] + 1, $range[1]);
        if (empty($codeTokens)) {
            return null;
        }
        $token = array_shift($codeTokens);
        if (!is_array($token) || $token[0] !== T_RETURN) {
            throw Exception::create(Exception::ERRORCODE_METHOD_TOO_COMPLEX, t('The body of the %s controller method is too complex', $methodName));
        }
        $token = array_pop($codeTokens);
        if ($token !== ';') {
            throw Exception::create(Exception::ERRORCODE_METHOD_TOO_COMPLEX, t('The body of the %s controller method is too complex', $methodName));
        }
        $codeTokens = $this->stripTFunctionCall($codeTokens);
        $codeTokens = $this->stripEnclosingParenthesis($codeTokens);
        if (count($codeTokens) !== 1 || !is_array($codeTokens[0])) {
            throw Exception::create(Exception::ERRORCODE_METHOD_TOO_COMPLEX, t('The body of the %s controller method is too complex', $methodName));
        }

        return $this->decodeValueToken($codeTokens, $methodName);
    }

    /**
     * Get token range of the body of a very simple class method (that is, containing only a "return something;").
     *
     * @param array $tokens the PHP tokens of the package controlller.php file
     * @param string $methodName
     * @param int $classStart the index of the first PHP token of the class body (its first '{')
     * @param int $classEnd the index of the last PHP token of the class body (its last '}'}
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return int[]|null returns null if it can't be detected (for example, for abstract methods), the token index of the opening '{' and the token index of the closing '}' otherwise
     */
    protected function getSimpleMethodBodyTokenRange(array $tokens, $methodName, $classStart, $classEnd)
    {
        $nestingLevel = 0;
        for ($index = $classStart + 1; $index < $classEnd; ++$index) {
            $token = $tokens[$index];
            if ($token === '{') {
                ++$nestingLevel;
            } elseif ($token === '}') {
                --$nestingLevel;
            } elseif ($nestingLevel === 0 && is_array($token) && $token[0] === T_FUNCTION) {
                $nameIndex = $this->skipNonCodeTokens($tokens, $index + 1, $classEnd);
                if ($nameIndex !== null && is_array($tokens[$nameIndex]) && $tokens[$nameIndex][0] === T_STRING && strcasecmp($tokens[$nameIndex][1], $methodName) === 0) {
                    $bodyStart = $this->findTextToken($tokens, ['{'], $nameIndex + 1, $classEnd);
                    if ($bodyStart === null) {
                        throw Exception::create(Exception::ERRORCODE_MISSING_METHOD_BODY, t('Missing body of the %s controller method', $methodName));
                    }
                    $bodyEnd = $this->findTextToken($tokens, ['}'], $bodyStart + 1, $classEnd);
                    if ($bodyEnd === null) {
                        throw Exception::create(Exception::ERRORCODE_MISSING_METHOD_BODY, t('Missing body of the %s controller method', $methodName));
                    }
                    if ($this->findTextToken($tokens, ['{'], $bodyStart + 1, $bodyEnd) !== null) {
                        throw Exception::create(Exception::ERRORCODE_METHOD_TOO_COMPLEX, t('The body of the %s controller method is too complex', $methodName));
                    }

                    return [$bodyStart, $bodyEnd];
                }
            }
        }

        return null;
    }

    /**
     * Check if a version is valid.
     *
     * @param string|mixed $version
     *
     * @return bool
     */
    protected function isVersionValid($version)
    {
        if (!is_string($version) || $version === '') {
            return false;
        }

        return preg_match('/^\d+(\.\d+)*([\w\-]+(\d+(\.\d+)*)?)?$/', $version);
    }

    /**
     * Find the index of a token given its type.
     *
     * @param array $tokens the list of PHP tokens where the search should be performed
     * @param int[] $tokenIDs the list of token identifiers to be searched
     * @param int $startIndexInclusive the initial index where the search should start (inclusive)
     * @param int|null $endIndexExclusive the final index where the search should end (exclusive); if null we'll search until the end of the token list
     *
     * @return int|null the index of the first token with an ID included in $tokenIDs otherwise; return NULL of none of the token ID have been found
     */
    protected function findTypedToken(array $tokens, array $tokenIDs, $startIndexInclusive = 0, $endIndexExclusive = null)
    {
        if ($endIndexExclusive === null) {
            $endIndexExclusive = count($tokens);
        }
        for ($index = $startIndexInclusive; $index < $endIndexExclusive; ++$index) {
            $token = $tokens[$index];
            if (is_array($token) && in_array($token[0], $tokenIDs, true)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Find the index of a text token given its contents.
     *
     * @param array $tokens the list of PHP tokens where the search should be performed
     * @param string[] $tokenContent the list of token values to be searched
     * @param int $startIndexInclusive the initial index where the search should start (inclusive)
     * @param int|null $endIndexExclusive the final index where the search should end (exclusive); if null we'll search until the end of the token list
     *
     * @return int|null the index of the first string token found; return NULL of none of the strings have been found
     */
    protected function findTextToken(array $tokens, array $tokenContent, $startIndexInclusive = 0, $endIndexExclusive = null)
    {
        if ($endIndexExclusive === null) {
            $endIndexExclusive = count($tokens);
        }
        for ($index = $startIndexInclusive; $index < $endIndexExclusive; ++$index) {
            if (in_array($tokens[$index], $tokenContent, true)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Strip whitespaces and comments from a list of tokens.
     *
     * @param array $tokens the list of PHP tokens to be processed
     * @param int $startIndexInclusive the initial index where the search should start (inclusive)
     * @param int|null $endIndexExclusive the final index where the search should end (exclusive); if null we'll search until the end of the token list
     *
     * @return array
     */
    protected function stripNonCodeTokens(array $tokens, $startIndexInclusive = 0, $endIndexExclusive = null)
    {
        if ($endIndexExclusive === null) {
            $endIndexExclusive = count($tokens);
        }
        $result = [];
        for ($index = $startIndexInclusive; $index < $endIndexExclusive; ++$index) {
            $token = $tokens[$index];
            if (!is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $result[] = $token;
            }
        }

        return $result;
    }

    /**
     * Strip t() function calls at the beginning of a list of tokens tokens (if any).
     *
     * @param array $tokens the list of PHP tokens to be processed
     *
     * @return array
     */
    protected function stripTFunctionCall(array $tokens)
    {
        if (!isset($tokens[0]) || !is_array($tokens[0]) || $tokens[0][0] !== T_STRING) {
            return $tokens;
        }
        if (strcasecmp($tokens[0][1], 't') !== 0) {
            return $tokens;
        }
        array_shift($tokens);

        return $tokens;
    }

    /**
     * Strip enclosing parenthesis ("(...)") (if any) in a list of tokens.
     *
     * @param array $tokens the list of PHP tokens to be processed
     *
     * @return array
     */
    protected function stripEnclosingParenthesis(array $tokens)
    {
        $numTokens = count($tokens);
        for (; ;) {
            if ($numTokens < 2 || $tokens[0] !== '(' || $tokens[$numTokens - 1] !== ')') {
                return $tokens;
            }
            array_pop($tokens);
            array_shift($tokens);
            $numTokens -= 2;
        }
    }

    /**
     * Skip to the next non-whitespace and non-comment token.
     *
     * @param array $tokens the whole list of tokens
     * @param int $startIndexInclusive the initial index where the search should start (inclusive)
     * @param int|null $endIndexExclusive the final index where the search should end (exclusive); if null we'll search until the end of the token list
     *
     * @return int|null the next index of non-whitespace and non-comment token; NULL when arriving to the end of the array
     */
    protected function skipNonCodeTokens(array $tokens, $startIndexInclusive = 0, $endIndexExclusive = null)
    {
        if ($endIndexExclusive === null) {
            $endIndexExclusive = count($tokens);
        }
        for ($index = $startIndexInclusive; $index < $endIndexExclusive; ++$index) {
            $token = $tokens[$index];
            if (!is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Get the actual PHP value described by a value token.
     *
     * @param array $token the token containing the value
     * @param string $associatedName the name associated to the value (used to display a useful error message),
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return mixed
     */
    protected function decodeValueToken(array $token, $associatedName)
    {
        switch ($token[0][0]) {
            case T_CONSTANT_ENCAPSED_STRING:
                return $this->decodeString($token[0][1]);
            case T_DNUMBER:
                return (float) $token[0][1];
            case T_LNUMBER:
                return (int) $token[0][1];
            default:
                throw Exception::create(Exception::ERRORCODE_UNSUPPORTED_TOKENVALUE, t('Unsupported value %1$s for %2$s', token_name($token[0][0]), $associatedName));
        }
    }

    /**
     * Decode the value of a T_CONSTANT_ENCAPSED_STRING token.
     *
     * @param string $string the value of a T_CONSTANT_ENCAPSED_STRING token
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return string
     */
    protected function decodeString($string)
    {
        $len = strlen($string);
        if ($len < 2) {
            throw Exception::create(Exception::ERRORCODE_INVALID_STRING_TOKEN, t('Malformed string: %s', $string));
        }
        if ($string[0] !== $string[$len - 1]) {
            throw Exception::create(Exception::ERRORCODE_INVALID_STRING_TOKEN, t('Malformed string: %s', $string));
        }
        $enclosing = $string[0];
        switch ($enclosing) {
            case '"':
                $escapeMap = [
                '\\' => '\\',
                '"' => '"',
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                'v' => "\v",
                'e' => "\e",
                'f' => "\f",
                '$' => '$',
                ];
            case "'":
                $escapeMap = [
                '\\' => '\\',
                "'" => "'",
                ];
                break;
            default:
                throw Exception::create(Exception::ERRORCODE_INVALID_STRING_TOKEN, t('Malformed string: %s', $string));
        }
        $string = substr($string, 1, -1);
        $result = '';
        for (; ;) {
            $p = strpos($string, '\\');
            if ($p === false || !isset($string[$p + 1])) {
                $result .= $string;
                break;
            }
            $nextChar = $string[$p + 1];
            $string = substr($string, 2);
            if (isset($escapeMap[$nextChar])) {
                $result .= $escapeMap[$nextChar];
            } else {
                $result .= '\\' . $nextChar;
            }
        }

        return $result;
    }
}
