<?php

namespace Concrete\Core\Package\Offline;

use Concrete\Core\Utility\Service\Validation\Strings as StringValidator;

abstract class Parser
{
    /**
     * @var \Concrete\Core\Utility\Service\Validation\Strings
     */
    protected $stringValidator;

    /**
     * @param \Concrete\Core\Utility\Service\Validation\Strings $stringValidator
     */
    public function __construct(StringValidator $stringValidator)
    {
        $this->stringValidator = $stringValidator;
    }

    /**
     * Determine if this parser can be used for the tokens specified.
     *
     * @param array $tokens
     *
     * @return bool
     */
    abstract public function canParseTokens(array $tokens);

    /**
     * Extract package details from the controller.php tokens.
     *
     *
     * @param array $tokens
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
     * Get the regular expression that the controller class name must match.
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
     * @param array $tokens
     * @param string $className
     * @param int $classStart
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    abstract protected function getPackageHandleFromClassName(array $tokens, $className, $classStart);

    /**
     * Get the data about the controller class.
     *
     *
     * @param array $tokens
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return array[] [$className, $classStart, $classEnd]
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
     * @param array $tokens
     * @param string $className
     * @param string $classStart
     * @param string $classEnd
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
     * @param array $tokens
     * @param string $className
     * @param string $classStart
     * @param string $classEnd
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
     * @param array $tokens
     * @param string $className
     * @param string $classStart
     * @param string $classEnd
     */
    protected function getPackageName(array $tokens, $className, $classStart, $classEnd)
    {
        $fromMethod = (string) $this->getMethodReturnValue($tokens, 'getPackageName', $classStart, $classEnd);
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
     * Get the package name.
     *
     * @param array $tokens
     * @param string $className
     * @param string $classStart
     * @param string $classEnd
     */
    protected function getPackageDescription(array $tokens, $className, $classStart, $classEnd)
    {
        $fromMethod = (string) $this->getMethodReturnValue($tokens, 'getPackageDescription', $classStart, $classEnd);
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
     * Get the package version.
     *
     * @param array $tokens
     * @param string $className
     * @param string $classStart
     * @param string $classEnd
     */
    protected function getPackageMinimumCodeVersion(array $tokens, $className, $classStart, $classEnd)
    {
        $fromProperty = $this->getPropertyValue($tokens, '$appVersionRequired', $classStart, $classEnd);

        return (string) $fromProperty === '' ? $this->getDefaultPackageMinimumCodeVersion() : $fromProperty;
    }

    /**
     * Get the value of a class property.
     *
     * @param array $tokens
     * @param string $propertyName
     * @param int $classStart
     * @param int $classEnd
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
                switch ($valueTokens[0][0]) {
                    case T_CONSTANT_ENCAPSED_STRING:
                        return $this->decodeString($valueTokens[0][1]);
                    case T_DNUMBER:
                        return (float) $valueTokens[0][1];
                    case T_LNUMBER:
                        return (int) $valueTokens[0][1];
                    default:
                        throw Exception::create(Exception::ERRORCODE_UNSUPPORTED_TOKENVALUE, t('Unsupported value %1$s for property %2s', token_name($valueTokens[0][0]), $propertyName));
                }
            }
        }

        return null;
    }

    /**
     * Get the return value of a class method.
     *
     * @param array $tokens
     * @param string $methodName
     * @param int $classStart
     * @param int $classEnd
     *
     * @return mixed
     */
    protected function getMethodReturnValue(array $tokens, $methodName, $classStart, $classEnd)
    {
        $range = $this->getMethodBodyTokenRange($tokens, $methodName, $classStart, $classEnd);
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
        switch ($codeTokens[0][0]) {
            case T_CONSTANT_ENCAPSED_STRING:
                return $this->decodeString($codeTokens[0][1]);
            case T_DNUMBER:
                return (float) $codeTokens[0][1];
            case T_LNUMBER:
                return (int) $codeTokens[0][1];
            default:
                throw Exception::create(Exception::ERRORCODE_UNSUPPORTED_TOKENVALUE, t('Unsupported return %1$s for method %2s', token_name($codeTokens[0][0]), $methodName));
        }
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
     * @param array $tokens
     * @param int[] $tokenIDs
     * @param int $startIndexInclusive
     * @param int|null $endIndexExclusive
     *
     * @return int|null
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
     * @param array $tokens
     * @param array $tokenContent
     * @param int $startIndexInclusive
     * @param int|null $endIndexExclusive
     *
     * @return int|null
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
     * Strip whitespaces and comments from tokens.
     *
     * @param array $tokens
     * @param int $startIndexInclusive
     * @param null|mixed $endIndexExclusive
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
     * Strip t() function calls at the beginning of tokens (if any).
     *
     * @param array $tokens
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
     * Strip enclosing parenthesis ("(...)") (if any).
     *
     * @param array $tokens
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
     * Skip to the next non-whitespaces and non-comment from token index.
     *
     * @param array $tokens
     * @param int $startIndexInclusive
     * @param null|mixed $endIndexExclusive
     *
     * @return int|null
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
     * Decode a T_CONSTANT_ENCAPSED_STRING string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function decodeString($string)
    {
        $len = strlen($string);
        if ($len < 2) {
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
        if ($string[$len - 1] !== $enclosing) {
            throw Exception::create(Exception::ERRORCODE_INVALID_STRING_TOKEN, t('Malformed string: %s', $string));
        }
        $result = '';
        $escaping = false;
        for ($i = 1; $i < $len - 1; ++$i) {
            $char = $string[$i];
            if ($escaping) {
                if (isset($escapeMap[$char])) {
                    $result .= $escapeMap[$char];
                } else {
                    $result .= '\\' . $char;
                }
                $escaping = false;
            } elseif ($char === '\\') {
                $escaping = true;
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    /**
     * Get token range of the body of a class method.
     *
     * @param array $tokens
     * @param string $methodName
     * @param int $classStart
     * @param int $classEnd
     *
     * @return int[]|null
     */
    private function getMethodBodyTokenRange(array $tokens, $methodName, $classStart, $classEnd)
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
}
