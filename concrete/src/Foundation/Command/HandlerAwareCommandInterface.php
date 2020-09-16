<?php

namespace Concrete\Core\Foundation\Command;

interface HandlerAwareCommandInterface extends CommandInterface
{

    public function getHandler() : string;

}
