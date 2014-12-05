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

        if ($facade === true || $facade !== false &&
            ($this->reflectionClass->isSubclassOf('\Concrete\Core\Support\Facade\Facade') ||
                $this->reflectionClass->isSubclassOf('\Illuminate\Support\Facades\Facade'))
        ) {
            $obj = $fqn::getFacadeRoot();

            $this->facade = true;
            $this->reflectionClass = new ReflectionClass($obj);
            $this->fqn = $this->reflectionClass->getName();
        } else {
            $this->facade = false;
        }

        $this->resolveMethods();
    }

    /**
     * Get the methods
     */
    protected function resolveMethods()
    {
        $methods = $this->reflectionClass->getMethods();
        foreach ($methods as $method) {
            $this->methods[] = new MethodSymbol($this, $method);
        }
    }

    /**
     * @return boolean
     */
    public function isFacade()
    {
        return !!$this->facade;
    }

    /**
     * Render Class with methods.
     *
     * @param string $eol
     * @param string $padding
     * @return string
     */
    public function render($eol = PHP_EOL, $padding = '    ')
    {
        $rendered = $eol . implode($eol, array_map('trim', explode($eol, $this->comment))) . $eol;
        $rendered .= 'class ' . $this->alias . ' extends ' . $this->fqn . "{$eol}{{$eol}{$eol}";
        $rendered .= '    /** @var ' . $this->fqn . ' */' . $eol . '    protected static $instance;' . $eol . $eol;
        foreach ($this->methods as $method) {
            $rendered_method = explode($eol, $method->render($eol, $padding));
            $rendered .= implode(
                $eol,
                array_map(
                    function ($val) use ($padding) {
                        if (substr($val, 0, 1) === '*') {
                            return $padding . $val;
                        }
                        return $padding . $val;
                    },
                    $rendered_method));
        }
        $rendered .= "{$eol}}{$eol}";
        return $rendered;
    }

}
