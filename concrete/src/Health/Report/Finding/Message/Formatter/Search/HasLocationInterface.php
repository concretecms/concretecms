<?php

namespace Concrete\Core\Health\Report\Finding\Message\Formatter\Search;

use Concrete\Core\Health\Report\Finding\Control\LocationInterface;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;

interface HasLocationInterface
{

    public function getLocation(MessageInterface $message): ?LocationInterface;


}
