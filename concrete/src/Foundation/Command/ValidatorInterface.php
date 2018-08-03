<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Error\ErrorList\ErrorList;

interface ValidatorInterface
{

    /**
     * @param CommandInterface $command
     * @return ErrorList
     */
    public function validate(CommandInterface $command);

}
