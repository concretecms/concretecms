<?php

namespace Concrete\Core\Foundation\Command;

interface HandlerAwareCommandInterface
{

    public static function getHandler() : string;

}
