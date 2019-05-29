<?php

namespace Concrete\Tests\Logging\Entry\User;

use Concrete\Core\Logging\Entry\User\LoginAttempt;
use PHPUnit_Framework_TestCase;

class LoginAttemptTest extends PHPUnit_Framework_TestCase
{

    public function testExpectedOutput()
    {
        $attempt = new LoginAttempt('foo', '/foobar', ['admin', 'bar'], []);

        $this->assertRegExp('/successful login attempt for .foo./i', $attempt->getMessage());
        $this->assertEquals([
            'groups' => ['admin', 'bar'],
            'errors' => [],
            'username' => 'foo',
            'requestPath' => '/foobar',
            'successful' => true
        ], $attempt->getContext());
    }

    public function testExpectedFailureOutput()
    {
        $attempt = new LoginAttempt('foo', '/derp', ['registered', 'baz'], ['Something wasn\'t quite right...']);

        $this->assertRegExp('/failed login attempt for .foo./i', $attempt->getMessage());
        $this->assertEquals([
            'groups' => ['registered', 'baz'],
            'requestPath' => '/derp',
            'errors' => ['Something wasn\'t quite right...'],
            'username' => 'foo',
            'successful' => false
        ], $attempt->getContext());
    }

    public function testSuccessIsBasedOnErrorList()
    {
        $attempt = new LoginAttempt('', [], []);
        $this->assertEquals(true, $attempt->getContext()['successful']);

        $attempt = new LoginAttempt('', '', [], ['']);
        $this->assertEquals(false, $attempt->getContext()['successful']);

        $attempt = new LoginAttempt('', '', [], ['', '', '']);
        $this->assertEquals(false, $attempt->getContext()['successful']);
    }

}
