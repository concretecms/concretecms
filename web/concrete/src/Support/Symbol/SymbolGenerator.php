<?php
/**
 * Concrete5 symbol file generator.
 * Inspired by Laravel IDE Helper Generator by Barry vd. Heuvel <barryvdh@gmail.com>
 */
namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Support\Symbol\ClassSymbol\ClassSymbol;

class SymbolGenerator
{

    /**
     * The ClassSymbol objects
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
     * @return mixed|string
     */
    public function render($eol = PHP_EOL, $padding = '    ')
    {
        $rendered = "<?php{$eol}namespace {{$eol}{$padding}die('Intended for use with IDE symbol matching only.');{$eol}";
        foreach ($this->classes as $class) {
            $rendered_class = explode($eol, $class->render($eol, $padding));
            $rendered .= $eol . implode(
                    $eol,
                    array_map(
                        function ($val) use ($padding) {
                            if (substr($val, 0, 1) === '*') {
                                return $padding . $val;
                            }
                            return $padding . $val;
                        },
                        $rendered_class));
        }
        $rendered .= "{$eol}}{$eol}";
        $rendered = implode($eol, array_map("rtrim", explode($eol, $rendered)));
        $rendered = preg_replace("~{$eol}{2,}~", "{$eol}{$eol}", $rendered);
        return $rendered;
    }

}
