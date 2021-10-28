<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Error\ErrorList\ErrorList;

interface ValidatorInterface
{

    /**
     * @param mixed $command
     * @return ErrorList
     */
    public function validate($command);

}
