<?php

namespace Concrete\Tests\User\Login;

class MockUser
{

    /**
     * Simple entry point for changing whether derived user objects are errored
     *
     * @var bool
     */
    public static $error;

    /**
     * The given constructor input
     *
     * @var array
     */
    public $input = [];

    /**
     * Whether our isError method has ever been called
     *
     * @var bool
     */
    public $isErrorCalled = false;

    /**
     * Whether our getError method has ever been called
     *
     * @var bool
     */
    public $getErrorCalled = false;

    public function __construct()
    {
        $this->input = func_get_args();
    }

    public function isError()
    {
        $this->isErrorCalled = true;
        return !is_null(static::$error);
    }

    public function getError()
    {
        $this->getErrorCalled = true;
        return static::$error;
    }
}
