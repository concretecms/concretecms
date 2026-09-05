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
     * Fully-qualified class alias name.
     *
     * @var string
     */
    protected $alias;

    /**
     * Namespace of the alias
     *
     * @var string
     */
    protected $aliasNamespace;

    /**
     * Base name of the alias (that is, without namespace)
     *
     * @var string
     */
    protected $aliasBasename;

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
        $this->fqn = ltrim($fqn, '\\');
        $this->alias = ltrim($alias, '/');
        $chunks = explode('\\', $this->alias);
        $this->aliasBasename = array_pop($chunks);
        $this->aliasNamespace = implode('\\', $chunks);
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
            $this->facade = $this->reflectionClass;
            $this->reflectionClass = $this->getFacadeRootReflectionClass($fqn);
            $this->fqn = $this->reflectionClass->getName();
        } else {
            $this->facade = null;
        }

        $this->resolveMethods();
    }

    /**
     * Get the reflection class of the object a facade forwards the calls to.
     *
     * @throws \Throwable if the facade root can't be resolved
     */
    protected function getFacadeRootReflectionClass(string $facadeClassName): ReflectionClass
    {
        try {
            return new ReflectionClass($facadeClassName::getFacadeRoot());
        } catch (\Throwable $x) {
            // The facade root can't be instantiated (for example because it requires a database connection, and Concrete is not installed):
            // let's fallback to the facade accessor, if it's a class/interface name
            $getFacadeAccessor = new \ReflectionMethod($facadeClassName, 'getFacadeAccessor');
            if (PHP_VERSION_ID < 80100) {
                // Not needed (and deprecated since PHP 8.5) in newer PHP versions
                $getFacadeAccessor->setAccessible(true);
            }
            $accessor = $getFacadeAccessor->invoke(null);
            if (is_string($accessor)) {
                if (class_exists($accessor) || interface_exists($accessor)) {
                    return new ReflectionClass($accessor);
                }
                // The accessor may be a container alias of a class name
                $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
                if ($app->isAlias($accessor)) {
                    $aliased = $app->getAlias($accessor);
                    if (is_string($aliased) && (class_exists($aliased) || interface_exists($aliased))) {
                        return new ReflectionClass($aliased);
                    }
                }
            }
            throw $x;
        }
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
        if ($this->reflectionClass->isAbstract()) {
            $rendered .= 'abstract ';
        }
        $rendered .= 'class ' . $this->aliasBasename . ' extends \\' . $this->fqn . "{$eol}{{$eol}";
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

    /**
     * Get the namespace of the alias.
     *
     * @return string
     */
    public function getAliasNamespace()
    {
        return $this->aliasNamespace;
    }

    /**
     * Get the fully-qualified name of the actual class (for facades: the class the calls are forwarded to).
     */
    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * Get the reflection of the actual class (for facades: the class the calls are forwarded to).
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }
}
