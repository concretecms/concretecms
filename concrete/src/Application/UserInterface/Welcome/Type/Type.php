<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

abstract class Type implements TypeInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;


}
