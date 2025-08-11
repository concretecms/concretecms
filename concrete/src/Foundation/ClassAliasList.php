<?php

namespace Concrete\Core\Foundation;

/**
 * @deprecated
 *
 * @see \Concrete\Core\Foundation\ClassAutoloader
 */
class ClassAliasList
{
    private static $loc;

    public function getRegisteredAliases()
    {
        return ClassAutoloader::getInstance()->getClassAliases();
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
        ClassAutoloader::getInstance()->addClassAlias($alias, $class, false);
    }

    public function registerMultiple($array)
    {
        ClassAutoloader::getInstance()->addClassAliases($array, false);
    }

    public function registerRequired($alias, $class)
    {
        ClassAutoloader::getInstance()->addClassAlias($alias, $class, true);
    }

    public function registerMultipleRequired($array)
    {
        ClassAutoloader::getInstance()->addClassAliases($array, true);
    }

    public function resolveRequired()
    {
        ClassAutoloader::getInstance()->autoloadAliasesAtBoot();
    }

    public function __get($name)
    {
        if ($name === 'aliases') {
            return ClassAutoloader::getInstance()->getClassAliases();
        }
        $trace = debug_backtrace();
        trigger_error("Undefined property {$name} in {$trace[0]['file']} on line {$trace[0]['line']}", E_USER_NOTICE);

        return null;
    }

    public function __set($name, $value)
    {
        if ($name === 'aliases') {
            ClassAutoloader::getInstance()->addClassAliases($value, false);
        }
        $trace = debug_backtrace();
        trigger_error("Undefined property {$name} in {$trace[0]['file']} on line {$trace[0]['line']}", E_USER_NOTICE);
    }
}
