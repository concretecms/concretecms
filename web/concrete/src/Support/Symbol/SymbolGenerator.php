<?php
/**
 * Concrete5 symbol file generator.
 * Inspired by Laravel IDE Helper Generator by Barry vd. Heuvel <barryvdh@gmail.com>
 */
namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Support\Symbol\ClassSymbol\ClassSymbol;

class SymbolGenerator {

    /**
     * The ClassSymbol objects
     * @var ClassSymbol[]
     */
    protected $classes = array();

    public function __construct() {
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
     * @param $alias string
     * @param $class string
     */
    public function registerClass($alias, $class) {
        $this->classes[$alias] = new ClassSymbol($alias, $class);
    }

    /**
     * Render the classes.
     * @return string
     */
    public function render() {
        $rendered = "<?php\nnamespace {\n    die('Intended for use with IDE symbol matching only.');\n";
        foreach ($this->classes as $class) {
            $rendered_class = explode("\n", $class->render());
            $rendered .= "\n" . implode("\n", array_map(function($val) {
                                             if (substr($val, 0, 1) === '*') {
                                                 return '     ' . $val;
                                             }
                                             return '    ' . $val;
                                         }, $rendered_class));
        }
        $rendered .= "\n}\n";
        $rendered = implode("\n", array_map("rtrim", explode("\n", $rendered)));
        $rendered = preg_replace("~\n{2,}~", "\n\n", $rendered);
        return $rendered;
    }

}
