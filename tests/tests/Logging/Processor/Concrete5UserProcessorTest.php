<?php
namespace Concrete\Tests\Logging\Processor;

use Concrete\Core\Application\Application;
use Concrete\Core\Logging\Processor\Concrete5UserProcessor;
use Concrete\Core\User\User;
use Mockery as M;
use PHPUnit_Framework_TestCase;

class Concrete5UserProcessorTest extends PHPUnit_Framework_TestCase
{

    public function testProcessorAddsUserDetails()
    {
        $user = M::mock(User::class);
        $user->shouldReceive('getUserID')->andReturn(1337);
        $user->shouldReceive('getUserName')->andReturn('foobaz');
        $user->shouldReceive('isRegistered')->andReturn(true);

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->withArgs([User::class])->andReturn($user);

        // Set up the processor
        $processor = new Concrete5UserProcessor($app);

        // Set up our input
        $given = [];
        array_set($given, 'some.other.key.that.should.pass.through', 'foo');
        array_set($given, 'extra.foo', 'baz');

        // Set up our expected output
        $expected = $given;
        array_set($expected, 'extra.user', [
            1337,
            'foobaz'
        ]);

        // Test that the user data gets added to the extra output
        $this->assertEquals($expected, $processor($given));
    }

    public function testProcessorIgnoresLoggedOutUsers()
    {
        $user = M::mock(User::class);
        $user->shouldReceive('getUserID')->andReturn(1337);
        $user->shouldReceive('getUserName')->andReturn('foobaz');
        $user->shouldReceive('isRegistered')->andReturn(false);

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->withArgs([User::class])->andReturn($user);

        // Set up the processor
        $processor = new Concrete5UserProcessor($app);

        // Set up our input
        $given = [];
        array_set($given, 'some.other.key.that.should.pass.through', 'foo');
        array_set($given, 'extra.foo', 'baz');

        // Test our given input is our output
        $this->assertEquals($given, $processor($given));
    }

    public function testProcessorDoesntMakeMoreThanOneUser()
    {
        $user = M::mock(User::class);
        $user->shouldReceive('getUserID')->andReturn(1337);
        $user->shouldReceive('getUserName')->andReturn('foobaz');
        $user->shouldReceive('isRegistered')->andReturn(true);

        // Set up the IOC container making sure to flag the ->make() as happening only once
        $app = M::mock(Application::class);
        $app->shouldReceive('make')->once()->withArgs([User::class])->andReturn($user);

        // Set up the processor
        $processor = new Concrete5UserProcessor($app);

        // Run the processor twice
        $processor([]);
        $processor([]);
    }

}