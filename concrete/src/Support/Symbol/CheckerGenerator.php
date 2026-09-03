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
     * @var string|null
     */
    private $namespace;

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

    public function renderLines(string $padding = '    '): array
    {
        $lines = [];
        $lines[] = 'class Checker';
        $lines[] = '{';
        $first = true;
        foreach ($this->getMethods() as $method) {
            if ($first) {
                $first = false;
            } else {
                $lines[] = '';
            }
            $phpDocsLines = [];
            if (($descriptions = $method->getDescriptions()) !== []) {
                if ($phpDocsLines !== []) {
                    $phpDocsLines[] = '';
                }
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
            $lines[] = "{$padding}public function {$method->getName()}({$method->getArguments()}) {}";
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
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    private function listMethods(): array
    {
        $all = [];
        foreach ($this->classLister->getClassNames() as $className) {
            if (in_array(ObjectInterface::class, class_implements($className), true)) {
                $all = array_merge($all, $this->analyzeObjectInterfaceClass($className));
            }
        }
        foreach ($this->permissionKeysProvider->getCategoryHandles() as $categoryHandle) {
            $all = array_merge($all, $this->generateMethodsFromCategory($categoryHandle));
        }
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
     * @return \Concrete\Core\Support\Symbol\CheckerGenerator\Method[]
     */
    private function analyzeObjectInterfaceClass(string $objectInterfaceClassName): array
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
                    $defaultValue = null;
                    if (method_exists($parameter, 'getDefaultValueConstantName')) {
                        $defaultValue = $parameter->getDefaultValueConstantName();
                    }
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
