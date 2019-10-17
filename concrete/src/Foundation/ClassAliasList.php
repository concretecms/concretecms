<?php
namespace Concrete\Core\Foundation;

class ClassAliasList
{
    private static $loc = null;
    public $aliases = array();

    /**
     * List of class aliases to be resolved as soon as possible.
     * @deprecated it will be removed in future versions
     *
     * @var string[]
     */
    private $requiredAliases = [];

    public function getRegisteredAliases()
    {
        return $this->aliases;
    }

    public static function getInstance()
    {
        if (null === self::$loc) {
            self::$loc = new self();
        }

        return self::$loc;
    }

    public function register($alias, $class)
    {
        $this->aliases[$alias] = $class;
    }

    public function registerMultiple($array)
    {
        foreach ($array as $alias => $class) {
            $this->register($alias, $class);
        }
    }

    /**
     * Register a class alias to be resolved as soon as possible.
     * @deprecated Don't use this method: it will be removed in future versions (use register)
     * @param string $alias
     * @param string $class
     */
    public function registerRequired($alias, $class)
    {
        $this->register($alias, $class);
        $this->requiredAliases[] = $alias;
    }

    /**
     * Register a list of class aliases to be pre-resolved as soon as possible.
     * @deprecated Don't use this method: it will be removed in future versions (use registerMultiple)
     */
    public function registerMultipleRequired($array)
    {
        $this->registerMultiple($array);
        $this->requiredAliases = array_merge($this->requiredAliases, array_keys($array));
    }

    /**
     * Pre-load the class aliases marked as required.
     * @deprecated Don't use this method: it will be removed in future versions.
     */
    public function resolveRequired()
    {
        foreach ($this->requiredAliases as $requiredAlias) {
            class_exists($requiredAlias);
        }
    }
}
