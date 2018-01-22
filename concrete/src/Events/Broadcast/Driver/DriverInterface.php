<?php

namespace Concrete\Core\Events\Broadcast\Driver;

interface DriverInterface
{

    public function broadcast($channel, $message);


}