<?php

namespace Concrete\Core\Foundation\Environment;

class User
{
    /**
     * @var FunctionInspector
     */
    protected $functionInspector;

    /**
     * @param FunctionInspector $functionInspector
     */
    public function __construct(FunctionInspector $functionInspector)
    {
        $this->functionInspector = $functionInspector;
    }

    /**
     * Check if the current user is root/superuser.
     *
     * @return bool|null return true if the current user is root, false if not root, or NULL if we don't know
     */
    public function isSuperUser()
    {
        $result = null;
        if ($this->functionInspector->functionAvailable('posix_getuid')) {
            $uid = posix_getuid();
            $result = $uid === 0;
        }

        return $result;
    }
}
