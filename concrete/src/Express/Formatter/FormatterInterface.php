<?php

namespace Concrete\Core\Express\Formatter;

interface FormatterInterface
{
    /**
     * Format a mask using the standard format given a callable
     *
     * Ex: if you have attributes with handles `student_first_name` and `student_last_name`
     * `%student_last_name%, %student_first_name%`
     *
     * @param $mask
     * @param callable $matchHandler
     * @return mixed
     */
    public function format($mask, callable $matchHandler);
}
