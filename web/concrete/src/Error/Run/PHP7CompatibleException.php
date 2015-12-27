<?php

namespace Concrete\Core\Error\Run;

class PHP7CompatibleException extends \RuntimeException
{

    public static function fromThrowable(\Throwable $throwable)
    {
        if ($previous = $throwable->getPrevious()) {
            $previous = static::fromThrowable($previous);
        }

        $type = get_class($throwable);
        $compat = new PHP7CompatibleException(
            "({$type}) " . $throwable->getMessage(),
            $throwable->getCode(),
            $previous);

        $compat->file = $throwable->getFile();
        $compat->line = $throwable->getLine();

        return $compat;
    }

    /**
     * String representation of the exception
     * @link http://php.net/manual/en/exception.tostring.php
     * @return string the string representation of the exception.
     * @since 5.1.0
     */
    public function __toString()
    {
        parent::__toString();
    }

}
