<?php
namespace Concrete\Core\Express\Exception;

class InvalidClassLocationDefinedException extends \Exception
{

    public function __construct()
    {
        $this->message = t('Class location not specified, or is not a writable directory.');
    }

}
