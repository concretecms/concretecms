<?php
namespace Concrete\Tests\Core\Session;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Session\SessionFactory;
use Concrete\Core\Session\SessionFactoryInterface;

class SessionFactoryTest extends \PHPUnit_Framework_TestCase
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
        $session = $this->factory->createSession();

        $this->assertEquals($session, $this->request->getSession());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\SessionInterface', $this->request->getSession());
    }

    public function testHandlerConfiguration()
    {
        $config = $this->app['config'];
        $config['concrete.session'] = array('handler' => 'database', 'save_path' => '/tmp');

        // Make the private `getSessionHandler` method accessible
        $reflection = new \ReflectionClass(get_class($this->factory));
        $method = $reflection->getMethod('getSessionHandler');
        $method->setAccessible(true);

        // Make sure database session gives us something other than native file session
        $pdo_handler = $method->invokeArgs($this->factory, array($config));
        $this->assertNotInstanceOf(
            'Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler', $pdo_handler);

        $config['concrete.session.handler'] = 'file';
        // Make sure file session does give us native file session
        /** @var \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler $native_handler */
        $native_handler = $method->invokeArgs($this->factory, array($config));
        $this->assertInstanceOf('Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler', $native_handler);
    }
}
