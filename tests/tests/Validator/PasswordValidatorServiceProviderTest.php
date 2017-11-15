<?php
namespace Concrete\Core\Tests\Validator;

class PasswordValidatorServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInstance()
    {
        $app = \Core::make('app');

        $provider = new \Concrete\Core\Validator\PasswordValidatorServiceProvider($app);
        $provider->register();

        $this->assertEquals($app->make('validator/password'), $app->make('validator/password'), 'Password validator not bound as instance.');
    }

    public function testRegistered()
    {
        $app = new \Concrete\Core\Application\Application();

        $provider = new \Concrete\Core\Validator\PasswordValidatorServiceProvider($app);
        $provider->register();

        $this->assertTrue($app->bound('validator/password'));
    }
}
