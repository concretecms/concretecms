<?php

/**
 * Concrete5 symbol file generator.
 * Inspired by Laravel IDE Helper Generator by Barry vd. Heuvel <barryvdh@gmail.com>.
 */

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Support\Symbol\ClassSymbol\ClassSymbol;

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

    public function __construct()
    {
        $list = ClassAliasList::getInstance();
        foreach ($list->getRegisteredAliases() as $alias => $class) {
            if (!class_exists($class)) {
                echo "Error: $class doesn't exist.\n";
                continue;
            }
            $this->registerClass($alias, $class);
        }
    }

    /**
     * Register a class alias, and store it in the classes array.
     *
     * @param $alias string
     * @param $class string
     */
    public function registerClass($alias, $class)
    {
        $classSymbol = new ClassSymbol($alias, $class);
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
        $lines = [];
        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = '// Generated on ' . date('c');
        foreach ($this->aliasNamespaces as $namespace) {
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
            $lines[] = '}';
        }
        $lines[] = '';

        return implode($eol, $lines);
    }
}
