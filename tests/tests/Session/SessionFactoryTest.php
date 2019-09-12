<?php

namespace Concrete\Tests\Session;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Session\SessionFactory;
use Concrete\Core\Session\SessionFactoryInterface;
use Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler;
use PHPUnit_Framework_TestCase;

class SessionFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Application */
    protected $app;

    /** @var Request */
    protected $request;

    /** @var SessionFactoryInterface */
    protected $factory;

    public function setUp()
    {
        $this->app = clone \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $this->app['config'] = clone $this->app['config'];

        $this->request = Request::create('http://url.com/');
        $this->factory = new SessionFactory($this->app, $this->request);
    }

    public function tearDown()
    {
        $this->app = $this->request = null;
    }

    public function testCreatesSession()
    {
        $session = $this->factory->createSession();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\SessionInterface', $session);
    }

    /**
     * This should be removed and moved into a request middleware layer, lets just make sure it happens here for now.
     */
    public function testAddedToRequest()
    {
        $this->app[Request::class] = $this->request;

        $session = $this->factory->createSession();

        $this->assertEquals($session, $this->request->getSession());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\SessionInterface', $this->request->getSession());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testHandlerConfiguration()
    {
        // Make the private `getSessionHandler` method accessible
        $reflection = new \ReflectionClass(get_class($this->factory));
        $method = $reflection->getMethod('getSessionHandler');
        $method->setAccessible(true);

        // Make sure database session gives us something other than native file session
        $pdo_handler = $method->invokeArgs($this->factory, [['handler' => 'database', 'save_path' => '/tmp']]);
        $this->assertNotInstanceOf(
            \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler::class, $pdo_handler);

        $config['concrete.session.handler'] = 'file';
        // Make sure file session does give us native file session
        /** @var \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler $native_handler */
        $native_handler = $method->invokeArgs($this->factory, [['handler' => 'file', 'save_path' => '/tmp']]);
        $this->assertInstanceOf(NativeFileSessionHandler::class, $native_handler);
    }
}
