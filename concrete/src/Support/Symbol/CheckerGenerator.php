<?php

declare(strict_types=1);

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Support\Symbol\CheckerGenerator\CIFPermissionKeysProvider;
use Concrete\Core\Support\Symbol\CheckerGenerator\DatabasePermissionKeysProvider;
use Concrete\Core\Support\Symbol\CheckerGenerator\Method;
use Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class CheckerGenerator
{
    /**
     * @var \Concrete\Core\Support\Symbol\ClassLister
     */
    private $classLister;

    /**
     * @var \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface
     */
    private $permissionKeysProvider;

    /**
     * @var \Concrete\Core\Support\Symbol\PhpDocTypeResolver
     */
    private $typeResolver;

    /**
     * @var \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]|null
     */
    private $methods;

    /**
     * @var array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]>|null array keys are the fully-qualified names of the permission response classes
     */
    private $responseClassMethods;

    /**
     * @var string|null
     */
    private $namespace;

    /**
     * @var array<string, string>|null array keys are the permission key handles, array values are the fully-qualified names of the classes implementing them
     */
    private $permissionKeyClassNames;

    /**
     * @param bool $isInstalled is Concrete installed? If so, the permission keys are read from the database, otherwise from the CIF files of the core
     * @param \Concrete\Core\Support\Symbol\CheckerGenerator\PermissionKeysProviderInterface|null $permissionKeysProvider a custom provider of the permission keys (if NULL, we'll use the default one depending on $isInstalled)
     * @param \Concrete\Core\Support\Symbol\ClassLister|null $classLister the lister of the core classes (if NULL, we'll create a new one)
     */
    public function __construct(FileService $fileService, bool $isInstalled, ?PermissionKeysProviderInterface $permissionKeysProvider = null, ?ClassLister $classLister = null)
    {
        if ($permissionKeysProvider === null) {
            $permissionKeysProvider = $isInstalled ? new DatabasePermissionKeysProvider() : new CIFPermissionKeysProvider($fileService, DIR_BASE_CORE . '/config/install');
        }
        $this->permissionKeysProvider = $permissionKeysProvider;
        $this->classLister = $classLister ?? new ClassLister($fileService, 'Concrete\Core', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);
        $this->typeResolver = new PhpDocTypeResolver();
    }

    public function getNamespace(): string
    {
        if ($this->namespace === null) {
            $fqName = Checker::class;
            $p = strrpos($fqName, '\\');
            $this->namespace = $p === false ? '' : substr($fqName, 0, $p);
        }

        return $this->namespace;
    }

    /**
     * Get the classes implementing the permission keys (for example, the add_block key is implemented by the AddBlockBlockTypeKey class).
     *
     * @return array<string, string> array keys are the permission key handles, array values are the fully-qualified names of the classes implementing them
     */
    public function getPermissionKeyClassNames(): array
    {
        if ($this->permissionKeyClassNames === null) {
            $result = [];
            foreach ($this->permissionKeysProvider->getCategoryHandles() as $categoryHandle) {
                foreach ($this->permissionKeysProvider->getKeys($categoryHandle) as $key) {
                    if ($key->getClassName() !== '') {
                        $result[$key->getHandle()] = ltrim($key->getClassName(), '\\');
                    }
                }
            }
            ksort($result, SORT_STRING);
            $this->permissionKeyClassNames = $result;
        }

        return $this->permissionKeyClassNames;
    }

    public function renderLines(string $padding = '    '): array
    {
        return self::renderMethodsClassLines('Checker', $this->getMethods(), $padding);
    }

    /**
     * Render the classes describing the methods of the permission response classes that are handled by __call().
     *
     * @return array<string, string[]> array keys are the fully-qualified names of the permission response classes, array values are the lines
     */
    public function renderResponseClassesLines(string $padding = '    '): array
    {
        $result = [];
        foreach ($this->getResponseClassMethods() as $responseClassName => $methods) {
            $p = strrpos($responseClassName, '\\');
            $result[$responseClassName] = self::renderMethodsClassLines($p === false ? $responseClassName : substr($responseClassName, $p + 1), $methods, $padding);
        }

        return $result;
    }

    /**
     * Render a class declaring the specified methods.
     *
     * @param \Concrete\Core\Support\Symbol\CheckerGenerator\Method[] $methods
     *
     * @return string[]
     */
    public static function renderMethodsClassLines(string $shortClassName, array $methods, string $padding = '    '): array
    {
        $lines = [];
        $lines[] = "class {$shortClassName}";
        $lines[] = '{';
        $first = true;
        foreach ($methods as $method) {
            if ($first) {
                $first = false;
            } else {
                $lines[] = '';
            }
            $phpDocsLines = [];
            if (($descriptions = $method->getDescriptions()) !== []) {
                foreach ($descriptions as $description) {
                    foreach (explode("\n", $description) as $line) {
                        $phpDocsLines[] = $line;
                    }
                }
            }
            if (($forObjectOfClasses = $method->getForObjectOfClasses()) !== []) {
                if ($phpDocsLines !== []) {
                    $phpDocsLines[] = '';
                }
                $phpDocsLines[] = 'For objects of the following classes:';
                foreach ($forObjectOfClasses as $forObjectOfClass) {
                    $phpDocsLines[] = "- {$forObjectOfClass}";
                }
            }
            if (($categoryKeyHandles = $method->getCategoryKeyHandles()) !== []) {
                if ($phpDocsLines !== []) {
                    $phpDocsLines[] = '';
                }
                $phpDocsLines[] = 'Permission category handles: ' . implode(', ', $categoryKeyHandles);
            }
            if (($sees = $method->getSees()) !== []) {
                if ($phpDocsLines !== []) {
                    $phpDocsLines[] = '';
                }
                foreach ($sees as $see) {
                    $phpDocsLines[] = "@see \\{$see}";
                }
            }
            $returnType = $method->getReturnType();
            if ($returnType === '' || $returnType !== 'mixed' && preg_match('/^\??[A-Za-z_][A-Za-z0-9_\\\\]*$/', $returnType)) {
                $nativeReturnType = $returnType === '' ? '' : ": {$returnType}";
            } else {
                // Types that can't be expressed as native return types are described in the PHPDoc
                $nativeReturnType = '';
                if ($phpDocsLines !== []) {
                    $phpDocsLines[] = '';
                }
                $phpDocsLines[] = "@return {$returnType}";
            }
            if ($method->isDeprecated()) {
                if ($phpDocsLines !== []) {
                    $phpDocsLines[] = '';
                }
                $phpDocsLines[] = '@deprecated';
            }
            if ($phpDocsLines !== []) {
                $lines[] = "{$padding}/**";
                foreach ($phpDocsLines as $line) {
                    $lines[] = "{$padding} * {$line}";
                }
                $lines[] = "{$padding} */";
            }
            $lines[] = "{$padding}public function {$method->getName()}({$method->getArguments()}){$nativeReturnType} {}";
        }
        $lines[] = '}';

        return $lines;
    }

    /**
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    public function getMethods(): array
    {
        if ($this->methods === null) {
            $this->methods = $this->listMethods();
        }

        return $this->methods;
    }

    /**
     * Get the methods of the permission response classes that are handled by __call() (they correspond to the permission keys of their categories).
     *
     * @return array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]> array keys are the fully-qualified names of the permission response classes
     */
    public function getResponseClassMethods(): array
    {
        $this->getMethods();

        return $this->responseClassMethods;
    }

    /**
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    private function listMethods(): array
    {
        $all = [];
        $responseClassMethods = [];
        foreach ($this->classLister->getClassNames() as $className) {
            if (in_array(ObjectInterface::class, class_implements($className), true)) {
                $all = array_merge($all, $this->analyzeObjectInterfaceClass($className, $responseClassMethods));
            }
        }
        foreach ($this->permissionKeysProvider->getCategoryHandles() as $categoryHandle) {
            $all = array_merge($all, $this->generateMethodsFromCategory($categoryHandle));
        }
        ksort($responseClassMethods, SORT_STRING);
        $this->responseClassMethods = array_map([$this, 'mergeMethods'], $responseClassMethods);

        return $this->mergeMethods($all);
    }

    /**
     * Merge the compatible methods and sort them.
     *
     * @param \Concrete\Core\Support\Symbol\CheckerGenerator\Method[] $all
     *
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    private function mergeMethods(array $all): array
    {
        $merged = [];
        foreach ($all as $item) {
            foreach ($merged as $prev) {
                if ($prev->isCompatibleWith($item)) {
                    $prev->merge($item);
                    continue 2;
                }
            }
            $merged[] = $item;
        }
        usort($merged, static function (Method $a, Method $b): int {
            $cmp = strnatcasecmp($a->getName(), $b->getName());
            if ($cmp === 0) {
                $cmp = strnatcasecmp($a->getArguments(), $b->getArguments());
            }

            return $cmp;
        });

        return $merged;
    }

    /**
     * @param array<string, \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]> $responseClassMethods the methods handled by __call() of the permission response classes (array keys are the fully-qualified class names) found so far
     *
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    private function analyzeObjectInterfaceClass(string $objectInterfaceClassName, array &$responseClassMethods): array
    {
        $result = [];
        $objectInterfaceClass = new \ReflectionClass($objectInterfaceClassName);
        if ($objectInterfaceClass->isAbstract()) {
            return $result;
        }
        $objectInterfaceClassName = $objectInterfaceClass->getName();
        $objectInterfaceInstance = $objectInterfaceClass->newInstanceWithoutConstructor();
        /** @var \Concrete\Core\Permission\ObjectInterface $objectInterfaceInstance */
        if (!is_string($categoryHandle = $objectInterfaceInstance->getPermissionObjectKeyCategoryHandle())) {
            $categoryHandle = '';
        }
        if ($categoryHandle !== '') {
            $result = array_merge($result, $this->generateMethodsFromCategory($categoryHandle, $objectInterfaceClassName));
        }
        $responseClassName = $objectInterfaceInstance->getPermissionResponseClassName();
        $canonicalResponseClassName = null;
        if (!class_exists($responseClassName)) {
            switch ($objectInterfaceClassName) {
                case 'Concrete\Core\Workflow\BasicWorkflow':
                case 'Concrete\Core\Workflow\EmptyWorkflow':
                    $canonicalResponseClassName = '';
                    break;
            }
        }
        if ($canonicalResponseClassName === null) {
            $responseClass = new \ReflectionClass($responseClassName);
            $canonicalResponseClassName = $responseClass->getName();
        }
        if ($canonicalResponseClassName !== '') {
            $result = array_merge($result, $this->generateMethodsFromResponseClass($canonicalResponseClassName, $objectInterfaceClassName, $categoryHandle));
            if ($categoryHandle !== '') {
                // The permission response classes handle with __call() the methods corresponding to the permission keys of their category
                foreach ($this->generateMethodsFromCategory($categoryHandle, $objectInterfaceClassName) as $method) {
                    if (!$responseClass->hasMethod($method->getName())) {
                        $responseClassMethods[$canonicalResponseClassName][] = $method;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    private function generateMethodsFromCategory(string $categoryHandle, string $objectInterfaceClassName = ''): array
    {
        $result = [];
        foreach ($this->permissionKeysProvider->getKeys($categoryHandle) as $key) {
            $name = 'can' . camelcase($key->getHandle());
            $method = new Method($name);
            $method
                ->setReturnType('bool')
                ->addDescription($key->getDescription() ?: $key->getName())
                ->addForObjectOfClass($objectInterfaceClassName)
                ->addCategoryKeyHandle($categoryHandle)
            ;
            $result[] = $method;
        }

        return $result;
    }

    private function generateMethodsFromResponseClass(string $responseClassName, string $objectInterfaceClassName = '', string $categoryHandle = ''): array
    {
        $responseClass = new \ReflectionClass($responseClassName);
        $result = [];
        foreach ($responseClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodInfo) {
            if (!preg_match('/^can[A-Z]/', $methodInfo->getName())) {
                continue;
            }
            $params = [];
            foreach ($methodInfo->getParameters() as $parameter) {
                $param = '';
                if ($parameter->isArray()) {
                    $param .= 'array ';
                } else {
                    try {
                        if (is_object($parameter->getClass())) {
                            $param .= $parameter->getClass()->getName() . ' ';
                        }
                    } catch (\Throwable $_) {
                    }
                }
                if ($parameter->isPassedByReference()) {
                    $param .= '&';
                }
                $param .= '$' . $parameter->getName();

                if ($parameter->isOptional()) {
                    $defaultValue = $parameter->getDefaultValueConstantName();
                    if ($defaultValue) {
                        // Strip out wrong namespaces.
                        $matches = null;
                        if (preg_match('/.\\\\(\\w+)$/', $defaultValue, $matches) && defined($matches[1])) {
                            $defaultValue = $matches[1];
                        }
                    } else {
                        $v = $parameter->getDefaultValue();
                        switch (gettype($v)) {
                            case 'boolean':
                            case 'integer':
                            case 'double':
                            case 'NULL':
                                $defaultValue = json_encode($v);
                                break;
                            case 'string':
                                $defaultValue = '"' . addslashes($v) . '"';
                                break;
                            case 'array':
                                if (count($v)) {
                                    $defaultValue = trim(var_export($v, true));
                                } else {
                                    $defaultValue = 'array()';
                                }
                                break;
                            case 'object':
                            case 'resource':
                            default:
                                $defaultValue = trim(var_export($v, true));
                                break;
                        }
                    }
                    $param .= ' = ' . $defaultValue;
                }
                $params[] = $param;
            }
            $phpDoc = (string) $methodInfo->getDocComment();
            $method = new Method($methodInfo->getName(), implode(', ', $params));
            $method
                ->setReturnType($this->typeResolver->resolveReturnType($methodInfo))
                ->setDeprecated(str_contains($phpDoc, '@deprecated'))
                ->addForObjectOfClass($objectInterfaceClassName)
                ->addCategoryKeyHandle($categoryHandle)
                ->addSee($methodInfo->getDeclaringClass()->getName() . '::' . $methodInfo->getName() . '()')
            ;
            $result[] = $method;
        }

        return $result;
    }
}
