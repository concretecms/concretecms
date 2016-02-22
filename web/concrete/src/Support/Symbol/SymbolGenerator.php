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
    protected $classes = array();

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
        $this->classes[$alias] = new ClassSymbol($alias, $class);
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
        $rendered = "<?php{$eol}namespace {{$eol}{$padding}die('Intended for use with IDE symbol matching only.');{$eol}";
        $rendered .= $padding . "//Generated on " . date('r') . $eol;
        foreach ($this->classes as $class) {
            $rendered_class = $class->render($eol, $padding, $methodFilter);
            if ($rendered_class !== '') {
                $rendered .= $eol . $padding . str_replace($eol, $eol . $padding, rtrim($rendered_class)) . $eol;
            }
        }
        $rendered .= '}' . $eol;

        return $rendered;
    }
}
