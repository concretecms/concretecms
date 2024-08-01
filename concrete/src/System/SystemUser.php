<?php

declare(strict_types=1);

namespace Concrete\Core\System;

use Concrete\Core\Foundation\Environment\FunctionInspector;

class SystemUser
{
    /**
     * @var \Concrete\Core\Foundation\Environment\FunctionInspector
     */
    protected $functionInspector;

    public function __construct(FunctionInspector $functionInspector)
    {
        $this->functionInspector = $functionInspector;
    }

    public function getCurrentUserName(): string
    {
        if (($result = $this->getCurrentUserWithPosix()) !== '') {
            return $result;
        }
        if (($result = $this->getCurrentUserWithWhoAmI()) !== '') {
            return $result;
        }
        if (($result = $this->getCurrentUserFromEnv()) !== '') {
            return $result;
        }

        return '';
    }

    protected function getCurrentUserWithPosix(): string
    {
        if (!$this->functionInspector->functionAvailable('posix_geteuid') || !$this->functionInspector->functionAvailable('posix_getpwuid')) {
            return '';
        }
        $userID = posix_geteuid();
        if ($userID) {
            $userInfo = posix_getpwuid($userID);
            if ($userInfo !== false) {
                if (is_string($userInfo['name'] ?? null) && $userInfo['name'] !== '') {
                    return $userInfo['name'];
                }
            }
        }

        return '';
    }

    protected function getCurrentUserWithWhoAmI(): string
    {
        if (!$this->functionInspector->functionAvailable('exec')) {
            return '';
        }
        if (DIRECTORY_SEPARATOR === '\\') {
            $cmd = 'whoami.exe 2>NUL';
        } else {
            $cmd = 'whoami 2>/dev/null';
        }
        $output = [];
        $rc = -1;
        exec($cmd, $output, $rc);
        if ($rc === 0 && count($output) === 1 && $output[0] !== '') {
            $result = $output[0];
            if (DIRECTORY_SEPARATOR === '\\' && ($p = strpos($result, '\\')) !== false) {
                // On Windows whoami returns DOMAIN\USERNAME
                $result = substr($result, $p + 1);
            }
            return $result;
        }
        return '';
    }

    protected function getCurrentUserFromEnv(): string
    {
        if (!$this->functionInspector->functionAvailable('getenv')) {
            return '';
        }
        if (DIRECTORY_SEPARATOR === '\\') {
            $envVarName = 'USERNAME';
        } else {
            $envVarName = 'USER';
        }
        $envVarValue = getenv($envVarName);
        if (is_string($envVarValue) && $envVarValue !== '') {
            return $envVarValue;
        }

        return '';
    }
}
