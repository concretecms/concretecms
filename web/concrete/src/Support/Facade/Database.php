<?php
namespace Concrete\Core\Support\Facade;

class Database extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'database';
    }

    /**
     * This is overridden to allow passthru to `DatabaseManager`'s __call
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        switch (count($args)) {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }

}
