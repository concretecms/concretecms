<?php

namespace Concrete\Tests\Validator;

use PHPUnit_Framework_TestCase;

class UserNameValidatorServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function testIsInstance()
    {
        $app = \Core::make('app');

        $provider = new \Concrete\Core\Validator\PasswordValidatorServiceProvider($app);
        $provider->register();

        $this->assertSame($app->make('validator/user/name'), $app->make('validator/user/name'), 'Username validator not bound as instance.');
    }

    public function testRegistered()
    {
        $app = new \Concrete\Core\Application\Application();

        $provider = new \Concrete\Core\Validator\UsernameValidatorServiceProvider($app);
        $provider->register();

        $this->assertTrue($app->bound('validator/user/name'));
    }
}
