<?php

declare(strict_types=1);

namespace Concrete\Tests\System;

use CIInfo\DriverList;
use CIInfo\Driver;
use Concrete\Core\System\SystemUser;
use Concrete\Tests\TestCase;
use ReflectionMethod;

class SystemUserTest extends TestCase
{
    public function testGettingSystemUser(): void
    {
        $expectedUsername = $this->getExpectedUsername();
        $systemUser = app(SystemUser::class);
        $actualUsername = $systemUser->getCurrentUserName();
        if ($expectedUsername !== '') {
            $this->assertSame($expectedUsername, $actualUsername);
        } else {
            $this->assertNotSame('', $actualUsername);
        }
        foreach ([
            'getCurrentUserWithPosix',
            'getCurrentUserWithWhoAmI',
            'getCurrentUserFromEnv',
        ] as $methodName) {
            $this->assertContains(
                $this->invokeProtectedGetter($systemUser, $methodName),
                ['', $actualUsername],
                "Expecting an empty string or {$actualUsername} as a result of the {$methodName} method",
                DIRECTORY_SEPARATOR === '\\'
            );
        }
    }

    private function getExpectedUsername(): string
    {
        $list = new DriverList();
        $driver = $list->getDriverForEnvironment();
        switch ($driver === null ? '' : $driver->getHandle()) {
            case Driver\GithubActions::HANDLE:
                return 'runner';
        }
        return '';
    }

    private function invokeProtectedGetter(SystemUser $systemUser, string $methodName): string
    {
        $method = new ReflectionMethod($systemUser, $methodName);
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        return $method->invoke($systemUser);
    }
}
