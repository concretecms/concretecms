<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Resolve the types of method parameters and return values to fully-qualified names (for example, to be used in @method annotations).
 */
final class PhpDocTypeResolver
{
    /**
     * The PHPDoc types that don't need to be resolved.
     *
     * @var string[]
     */
    private const BUILTIN_TYPES = [
        'array',
        'bool',
        'callable',
        'false',
        'float',
        'int',
        'iterable',
        'mixed',
        'null',
        'object',
        'resource',
        'string',
        'true',
        'void',
    ];

    /**
     * Aliases of the builtin types.
     *
     * @var array<string, string>
     */
    private const BUILTIN_TYPE_ALIASES = [
        'boolean' => 'bool',
        'double' => 'float',
        'integer' => 'int',
        'real' => 'float',
        'scalar' => 'bool|float|int|string',
    ];

    /**
     * The imported classes of the already parsed files.
     *
     * @var array<string, array<string, string>> array keys are the file names, array values are arrays whose keys are the aliases and whose values are the fully-qualified class names
     */
    private $fileImports = [];

    /**
     * Get the return type of a method, from the native return type or (if not available) from the PHPDoc.
     *
     * @return string empty string if the return type can't be determined
     */
    public function resolveReturnType(\ReflectionMethod $method): string
    {
        $declaringClass = $method->getDeclaringClass();
        $nativeType = $method->getReturnType();
        if ($nativeType !== null) {
            return $this->resolveNativeType($nativeType, $declaringClass);
        }
        $phpDoc = $method->getDocComment();
        $matches = null;
        if ($phpDoc === false || !preg_match('/^\s*\*\s*@return\s+(\S+)/m', $phpDoc, $matches)) {
            return '';
        }

        return $this->resolvePhpDocType($matches[1], $declaringClass);
    }

    /**
     * Get the type of a method parameter, from the native type or (if not available) from the PHPDoc.
     *
     * @return string empty string if the type can't be determined
     */
    public function resolveParameterType(\ReflectionParameter $parameter): string
    {
        $method = $parameter->getDeclaringFunction();
        $declaringClass = $method instanceof \ReflectionMethod ? $method->getDeclaringClass() : null;
        $nativeType = $parameter->getType();
        if ($nativeType !== null) {
            return $this->resolveNativeType($nativeType, $declaringClass);
        }
        $phpDoc = $method->getDocComment();
        $matches = null;
        if ($phpDoc === false || !preg_match('/^\s*\*\s*@param\s+(\S+)\s+\.{0,3}\$' . preg_quote($parameter->getName(), '/') . '\b/m', $phpDoc, $matches)) {
            return '';
        }

        return $this->resolvePhpDocType($matches[1], $declaringClass);
    }

    /**
     * Resolve a type found in a PHPDoc block to fully-qualified class names.
     *
     * @param \ReflectionClass|null $context the class where the PHPDoc is defined
     *
     * @return string empty string if the type can't be resolved
     */
    public function resolvePhpDocType(string $type, ?\ReflectionClass $context): string
    {
        $type = trim($type);
        if ($type === '' || preg_match('/[<>(){}&:]/', $type)) {
            // Too complex to be resolved
            return '';
        }
        $resolved = [];
        foreach (explode('|', $type) as $chunk) {
            $chunk = trim($chunk);
            $nullable = false;
            if ($chunk !== '' && $chunk[0] === '?') {
                $nullable = true;
                $chunk = substr($chunk, 1);
            }
            $arraySuffix = '';
            $matches = null;
            if (preg_match('/^(.+?)((\[\])+)$/', $chunk, $matches)) {
                $chunk = $matches[1];
                $arraySuffix = $matches[2];
            }
            $resolvedChunk = $this->resolveSimplePhpDocType($chunk, $context);
            if ($resolvedChunk === '') {
                return '';
            }
            $resolvedChunk = ($nullable ? '?' : '') . $resolvedChunk . $arraySuffix;
            if (!in_array($resolvedChunk, $resolved, true)) {
                $resolved[] = $resolvedChunk;
            }
        }

        return implode('|', $resolved);
    }

    private function resolveNativeType(\ReflectionType $type, ?\ReflectionClass $context): string
    {
        if (!($type instanceof \ReflectionNamedType)) {
            // Union/intersection types (PHP 8+)
            if (method_exists($type, 'getTypes')) {
                $resolved = [];
                foreach ($type->getTypes() as $subType) {
                    $resolvedSubType = $this->resolveNativeType($subType, $context);
                    if ($resolvedSubType === '') {
                        return '';
                    }
                    $resolved[] = $resolvedSubType;
                }

                return implode('|', $resolved);
            }

            return '';
        }
        $name = $type->getName();
        $nullable = $type->allowsNull() && !in_array($name, ['mixed', 'null'], true);
        if ($type->isBuiltin()) {
            return ($nullable ? '?' : '') . $this->normalizeBuiltinType($name);
        }
        $resolved = $this->resolveClassLikeType($name, $context);
        if ($resolved === '') {
            return '';
        }

        return ($nullable ? '?' : '') . $resolved;
    }

    private function resolveSimplePhpDocType(string $type, ?\ReflectionClass $context): string
    {
        if ($type === '' || !preg_match('/^\\\\?[A-Za-z_\$][A-Za-z0-9_\\\\]*$/', $type)) {
            return '';
        }
        $lowerCaseType = strtolower($type);
        if (isset(self::BUILTIN_TYPE_ALIASES[$lowerCaseType])) {
            return self::BUILTIN_TYPE_ALIASES[$lowerCaseType];
        }
        if (in_array($lowerCaseType, self::BUILTIN_TYPES, true)) {
            return $this->normalizeBuiltinType($lowerCaseType);
        }

        return $this->resolveClassLikeType($type, $context);
    }

    /**
     * Make sure that we don't use builtin types that require a value type (in order to avoid PHPStan stub errors).
     */
    private function normalizeBuiltinType(string $type): string
    {
        switch ($type) {
            case 'array':
                return 'array<mixed>';
            case 'iterable':
                return 'iterable<mixed>';
            default:
                return $type;
        }
    }

    /**
     * @return string empty string if the class can't be resolved
     */
    private function resolveClassLikeType(string $type, ?\ReflectionClass $context): string
    {
        switch (strtolower($type)) {
            case 'self':
            case 'static':
            case '$this':
                return $context === null ? '' : '\\' . $context->getName();
            case 'parent':
                $parent = $context === null ? false : $context->getParentClass();

                return $parent === false ? '' : '\\' . $parent->getName();
        }
        if ($type[0] === '\\') {
            $fqn = substr($type, 1);
        } else {
            $chunks = explode('\\', $type);
            $imports = $context === null ? [] : $this->getFileImports($context);
            if (isset($imports[$chunks[0]])) {
                $chunks[0] = $imports[$chunks[0]];
                $fqn = implode('\\', $chunks);
            } elseif ($context !== null && $context->getNamespaceName() !== '') {
                $fqn = $context->getNamespaceName() . '\\' . $type;
            } else {
                $fqn = $type;
            }
        }
        try {
            if (!class_exists($fqn) && !interface_exists($fqn)) {
                return '';
            }
        } catch (\Throwable $_) {
            return '';
        }

        return '\\' . $fqn;
    }

    /**
     * Get the classes imported (with "use" statements) in the file where a class is defined.
     *
     * @return array<string, string> array keys are the aliases, array values are the fully-qualified class names
     */
    private function getFileImports(\ReflectionClass $class): array
    {
        $file = $class->getFileName();
        if (!is_string($file) || $file === '') {
            return [];
        }
        if (!isset($this->fileImports[$file])) {
            $this->fileImports[$file] = $this->parseFileImports($file);
        }

        return $this->fileImports[$file];
    }

    /**
     * @return array<string, string>
     */
    private function parseFileImports(string $file): array
    {
        $result = [];
        $contents = @file_get_contents($file);
        if (!is_string($contents)) {
            return $result;
        }
        $matches = null;
        if (preg_match_all('/^\s*use\s+(?!function\s|const\s)([^;]+);/m', $contents, $matches)) {
            foreach ($matches[1] as $useStatement) {
                $useStatement = preg_replace('/\s+/', ' ', trim($useStatement));
                $groupMatches = null;
                if (preg_match('/^(.+?)\\\\\s*\{(.+)\}$/', $useStatement, $groupMatches)) {
                    $prefix = trim($groupMatches[1]) . '\\';
                    $items = explode(',', $groupMatches[2]);
                } else {
                    $prefix = '';
                    $items = explode(',', $useStatement);
                }
                foreach ($items as $item) {
                    $item = trim($item);
                    if ($item === '') {
                        continue;
                    }
                    $itemMatches = null;
                    if (preg_match('/^(\S+)\s+as\s+(\S+)$/i', $item, $itemMatches)) {
                        $fqn = $prefix . ltrim($itemMatches[1], '\\');
                        $alias = $itemMatches[2];
                    } else {
                        $fqn = $prefix . ltrim($item, '\\');
                        $alias = substr(strrchr('\\' . $fqn, '\\'), 1);
                    }
                    $result[$alias] = $fqn;
                }
            }
        }

        return $result;
    }
}
