<?php

namespace Concrete\Core\Foundation\Command;

interface HandlerAwareCommandInterface extends CommandInterface
{

    public static function getHandler() : string;

}
