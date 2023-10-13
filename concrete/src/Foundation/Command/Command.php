<?php

namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Foundation\Command\Traits\HandlerAwareCommandTrait;

/**
 * This is a convenience superclass. You do NOT have to use this in order to use the command bus. This command
 * will automatically provide a handler for you in the same namespace as your command.
 *
 * @package Concrete\Core\Foundation\Command
 */
abstract class Command implements HandlerAwareCommandInterface
{

    use HandlerAwareCommandTrait;


}
