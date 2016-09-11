<?php

namespace Concrete\Core\Foundation;

/**
 * A class loader that uses the class alias list.
 */
class AliasClassLoader
{
    private $classAliasList;
    private $isLoaded = false;

    /**
     * Constructor.
     *
     * @param array $map A map where keys are classes and values the absolute file path
     */
    public function __construct(ClassAliasList $classAliasList)
    {
        $this->classAliasList = $classAliasList;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register($prepend = false)
    {
        if (!$this->isLoaded) {
            spl_autoload_register(array($this, 'loadClass'), true, $prepend);
        }
        $this->isLoaded = true;
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class
     */
    public function loadClass($class)
    {
        if (array_key_exists($class, $aliases = $this->classAliasList->getRegisteredAliases())) {
            // we have an alias for it, but we don't have it yet loaded
            // (because after all, we're in the auto loader.)
            $fullClass = $aliases[$class];
            if (!class_exists($fullClass, false)) {
                spl_autoload_call($fullClass);
            }
            // finally, we set up a class alias for this list. We do this now because
            // we don't know earlier what namespace it'll be in
            class_alias($fullClass, $class);
        }
    }

}
