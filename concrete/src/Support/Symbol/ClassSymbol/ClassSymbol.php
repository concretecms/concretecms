<?php
namespace Concrete\Core\Support\Symbol\ClassSymbol;

use Concrete\Core\Support\Symbol\ClassSymbol\MethodSymbol\MethodSymbol;
use ReflectionClass;

class ClassSymbol
{
    /**
     * Fully qualified class name.
     *
     * @var string
     */
    protected $fqn;

    /**
     * If the class is a facade, here we have the facade ReflectionClass, otherwise it's null.
     *
     * @var ReflectionClass|null
     */
    protected $facade;

    /**
     * Class alias.
     *
     * @var string
     */
    protected $alias;

    /**
     * Array of MethodSymbols.
     *
     * @var MethodSymbol[]
     */
    protected $methods = array();

    /**
     * The class's docblock.
     *
     * @var string
     */
    protected $comment;

    /**
     * The ReflectionClass instance.
     *
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @param $alias  string Class Alias
     * @param $fqn    string Fully qualified Class name
     * @param $facade bool Is this a facade
     */
    public function __construct($alias, $fqn, $facade = null)
    {
        $this->reflectionClass = new ReflectionClass($fqn);
        $this->fqn = $fqn;
        $this->alias = $alias;
        $this->comment = $this->reflectionClass->getDocComment();

        if (
            $facade === true
            ||
            (
                $facade !== false
                &&
                (
                    $this->reflectionClass->isSubclassOf('\Concrete\Core\Support\Facade\Facade')
                    ||
                    $this->reflectionClass->isSubclassOf('\Illuminate\Support\Facades\Facade')
                )
            )
        ) {
            $obj = $fqn::getFacadeRoot();

            $this->facade = $this->reflectionClass;
            $this->reflectionClass = new ReflectionClass($obj);
            $this->fqn = $this->reflectionClass->getName();
        } else {
            $this->facade = null;
        }

        $this->resolveMethods();
    }

    /**
     * Get the methods.
     */
    protected function resolveMethods()
    {
        $methods = $this->reflectionClass->getMethods();
        if ($this->isFacade()) {
            $methods = array_merge($methods, $this->getFacadeReflectionClass()->getMethods());
        }
        foreach ($methods as $method) {
            $this->methods[] = new MethodSymbol($this, $method);
        }
    }

    /**
     * @return bool
     */
    public function isFacade()
    {
        return isset($this->facade);
    }

    /**
     * @return ReflectionClass|null
     */
    public function getFacadeReflectionClass()
    {
        return $this->facade;
    }

    /**
     * Render Class with methods.
     *
     * @param string $eol
     * @param string $padding
     * @param callable|null $methodFilter
     *
     * @return string
     */
    public function render($eol = "\n", $padding = '    ', $methodFilter = null)
    {
        $rendered = '';
        $comment = $this->comment;
        if ($comment !== false) {
            $comment = trim($comment);
            if ($comment !== '') {
                $rendered .= str_replace($eol . '*', $eol . ' *', implode($eol, array_map('trim', explode("\n", $comment)))) . $eol;
            }
        }
        $rendered .= 'class ' . $this->alias . ' extends ' . $this->fqn . "{$eol}{{$eol}";
        $firstMethod = true;
        foreach ($this->methods as $method) {
            if (is_callable($methodFilter) && (call_user_func($methodFilter, $this, $method) === false)) {
                continue;
            }
            if ($firstMethod) {
                $firstMethod = false;
                if ($this->isFacade()) {
                    $rendered .= $padding . '/**' . $eol . $padding . ' * @var ' . $this->fqn . $eol . $padding . ' */' . $eol;
                    $rendered .= $padding . 'protected static $instance;' . $eol;
                }
            }
            $rendered_method = $method->render($eol, $padding);
            if ($rendered_method !== '') {
                $rendered .= $padding . rtrim(str_replace($eol, $eol . $padding, $rendered_method)) . $eol;
            }
        }
        $rendered .= "}{$eol}";

        return $rendered;
    }
}
