<?php

namespace Concrete\Core\Foundation\Command\Traits;

trait HandlerAwareCommandTrait
{

    public static function getHandler(): string
    {
        return static::class . 'Handler';
    }

}
