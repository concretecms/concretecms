<?php
namespace Concrete\Core\Support\Symbol\ClassSymbol;


use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Support\Symbol\ClassSymbol\MethodSymbol\MethodSymbol;
use ReflectionClass;

class ClassSymbol {

    /**
     * Fully qualified class name.
     * @var string
     */
    protected $fqn;

    /**
     * Class alias.
     * @var string
     */
    protected $alias;

    /**
     * Array of MethodSymbols.
     * @var array
     */
    protected $methods = array();

    /**
     * The class's docblock.
     * @var string
     */
    protected $comment;

    /**
     * The ReflectionClass instance.
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @param $alias string Class Alias
     * @param $fqn   string Fully qualified Class name
     */
    public function __construct($alias, $fqn) {
        $this->reflectionClass = new ReflectionClass($fqn);
        $this->fqn = $fqn;
        $this->alias = $alias;
        $this->comment = $this->reflectionClass->getDocComment();
        if (substr($fqn, 0, 29) === '\Concrete\Core\Support\Facade') {
            $new_fqn = $fqn::getFacadeAccessor();
            if (!class_exists($new_fqn)) {
                $list = ClassAliasList::getInstance();
                $aliases = $list->getRegisteredAliases();
                $new_fqn = $aliases[$fqn];
            }
            if (class_exists($new_fqn)) {
                $this->reflectionClass = new ReflectionClass($new_fqn);
                $this->fqn = $new_fqn;
            }
        }

        $this->resolveMethods();
    }

    /**
     * Get the methods
     */
    protected function resolveMethods() {
        $methods = $this->reflectionClass->getMethods();
        foreach($methods as $method) {
            $this->methods[] = new MethodSymbol($method);
        }
    }

    /**
     * Render Class with methods.
     * @return string
     */
    public function render() {
        $rendered = "\n" . implode("\n", array_map(trim, explode("\n", $this->comment))) . "\n";
        $rendered .= 'class ' . $this->alias . ' extends ' . $this->fqn . "\n{\n";
        foreach($this->methods as $method) {
            $rendered_method = explode("\n", $method->render());
            $rendered .= implode("\n", array_map(function($val) {
                                             if (substr($val, 0, 1) === '*') {
                                                 return '     ' . $val;
                                             }
                                             return '    ' . $val;
                                         }, $rendered_method));
        }
        $rendered .= "\n}\n";
        return $rendered;
    }

}
