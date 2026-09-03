<?php

/**
 * Concrete5 symbol file generator.
 * Inspired by Laravel IDE Helper Generator by Barry vd. Heuvel <barryvdh@gmail.com>.
 */

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Symbol\ClassSymbol\ClassSymbol;
use Throwable;

class SymbolGenerator
{
    /**
     * The ClassSymbol objects.
     *
     * @var ClassSymbol[]
     */
    protected $classes = [];

    /**
     * All the alias namespaces.
     *
     * @var array
     */
    protected $aliasNamespaces = [''];

    /**
     * The ClassSymbol objects of all the facades (array keys are the fully-qualified names of the facade classes).
     * NULL if not yet listed.
     *
     * @var ClassSymbol[]|null
     */
    protected $facades;

    /**
     * @var \Concrete\Core\Support\Symbol\ClassLister
     */
    protected $classLister;

    /**
     * @var \Concrete\Core\Support\Symbol\CheckerGenerator
     */
    protected $checkerGenerator;

    /**
     * @var bool
     */
    protected $isInstalled;

    public function __construct(?bool $isInstalled = null)
    {
        $this->isInstalled = $isInstalled ?? app()->isInstalled();
        $list = ClassAliasList::getInstance();
        foreach ($list->getRegisteredAliases() as $alias => $class) {
            if (!class_exists($class)) {
                echo "Error: $class doesn't exist.\n";
                continue;
            }
            $this->registerClass($alias, $class);
        }
        $this->classLister = new ClassLister(app(FileService::class), 'Concrete\Core', DIR_BASE_CORE . '/' . DIRNAME_CLASSES);
        $this->checkerGenerator = app(CheckerGenerator::class, ['isInstalled' => $this->isInstalled, 'classLister' => $this->classLister]);
    }

    public function getCheckerGenerator(): CheckerGenerator
    {
        return $this->checkerGenerator;
    }

    /**
     * Get the ClassSymbol objects of all the facades: the ones with a registered class alias, plus the ones found in the core classes.
     *
     * @return ClassSymbol[] array keys are the fully-qualified names of the facade classes
     */
    public function getFacades(): array
    {
        if ($this->facades === null) {
            $facades = [];
            foreach ($this->classes as $classSymbol) {
                if ($classSymbol->isFacade()) {
                    $facades[$classSymbol->getFacadeReflectionClass()->getName()] = $classSymbol;
                }
            }
            foreach ($this->classLister->getClassNames() as $className) {
                if (isset($facades[$className]) || !is_subclass_of($className, Facade::class)) {
                    continue;
                }
                try {
                    $classSymbol = new ClassSymbol($className, $className);
                } catch (Throwable $_) {
                    // The facade root can't be resolved (for example because Concrete is not installed)
                    continue;
                }
                $facades[$className] = $classSymbol;
            }
            ksort($facades, SORT_STRING);
            $this->facades = $facades;
        }

        return $this->facades;
    }

    /**
     * Register a class alias, and store it in the classes array.
     *
     * @param $alias string
     * @param $class string
     */
    public function registerClass($alias, $class)
    {
        if ($this->isInstalled) {
            $classSymbol = new ClassSymbol($alias, $class);
        } else {
            try {
                $classSymbol = new ClassSymbol($alias, $class);
            } catch (Throwable $_) {
                return;
            }
        }
        $this->classes[$alias] = $classSymbol;
        $aliasNamespace = $classSymbol->getAliasNamespace();
        if (!in_array($aliasNamespace, $this->aliasNamespaces, true)) {
            $this->aliasNamespaces[] = $aliasNamespace;
        }
    }

    /**
     * Render the classes.
     *
     * @param string $eol
     * @param string $padding
     * @param callable|null $methodFilter
     *
     * @return mixed|string
     */
    public function render($eol = "\n", $padding = '    ', $methodFilter = null)
    {
        $checkerWritten = false;
        $lines = [];
        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = '// Generated on ' . date('c');
        $namespaces = $this->aliasNamespaces;
        if (!in_array($this->checkerGenerator->getNamespace(), $namespaces, true)) {
            $namespaces[] = $this->checkerGenerator->getNamespace();
        }
        foreach ($namespaces as $namespace) {
            $lines[] = '';
            $lines[] = rtrim("namespace {$namespace}");
            $lines[] = '{';
            $addNewline = false;
            if ($namespace === '') {
                $lines[] = "{$padding}die('Access Denied.');";
                $addNewline = true;
            }
            foreach ($this->classes as $class) {
                if ($class->getAliasNamespace() === $namespace) {
                    $rendered_class = $class->render($eol, $padding, $methodFilter);
                    if ($rendered_class !== '') {
                        if ($addNewline === true) {
                            $lines[] = '';
                        } else {
                            $addNewline = true;
                        }
                        $lines[] = $padding . str_replace($eol, $eol . $padding, rtrim($rendered_class));
                    }
                }
            }
            if ($checkerWritten === false && $this->checkerGenerator->getNamespace() === $namespace) {
                foreach ($this->checkerGenerator->renderLines($padding) as $line) {
                    $lines[] = "{$padding}{$line}";
                }
                $checkerWritten = true;
            }
            $lines[] = '}';
        }
        $lines[] = '';

        return implode($eol, $lines);
    }
}
