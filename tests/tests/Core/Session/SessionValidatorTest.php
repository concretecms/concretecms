<?php

namespace Concrete\Tests\Core\Session;

use Concrete\Core\Http\Request;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\Support\Facade\Application;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionValidatorTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Concrete\Core\Application\Application */
    protected $app;

    /** @var Request */
    protected $request;

    /** @var SessionValidator */
    protected $validator;

    /** @var Session */
    protected $session;

    public function setUp()
    {
        $this->app = clone Application::getFacadeApplication();
        $this->app['config'] = clone $this->app['config'];

        $this->request = Request::create('http://url.com/');
        $this->validator = new SessionValidator($this->app, $this->app['config'], $this->request);

        $store = array();
        $mock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->setMethods(array('has', 'get', 'set', 'invalidate', 'all'))
            ->getMock();

        $mock->expects($this->any())
            ->method('has')->willReturnCallback(function($key) use (&$store) {
                return array_get($store, $key) !== null;
            });

        $mock->expects($this->any())
            ->method('get')->willReturnCallback(function($key, $default=null) use (&$store) {
                return array_get($store, $key, $default);
            });

        $mock->expects($this->any())
            ->method('set')->willReturnCallback(function($key, $value) use (&$store) {
                return $store[$key] = $value;
            });

        $mock->expects($this->any())
            ->method('all')->willReturnCallback(function() use (&$store) {
                return $store;
            });

        $this->session = $mock;
    }

    public function tearDown()
    {
        $this->session = $this->app = $this->validator = $this->request = null;
    }

    public function testSetsOnFirstCheck()
    {
        // Change client ip
        $this->request->server->set('REMOTE_ADDR', '111.112.113.114');
        $this->request->server->set('HTTP_USER_AGENT', 'TESTING');

        // Don't invalidate
        $this->session->expects($this->never())->method('invalidate');
        $this->validator->handleSessionValidation($this->session);

        $this->assertEquals($this->session->all(), array(
            'CLIENT_REMOTE_ADDR' => '111.112.113.114',
            'CLIENT_HTTP_USER_AGENT' => 'TESTING'));
    }

    public function testInvalidatesOnInvalidIP()
    {
        $logger = $this->getMockBuilder('Monolog\Logger')->setConstructorArgs(array(''))->enableArgumentCloning()->getMock();

        // Change client ip
        $this->request->server->set('REMOTE_ADDR', '111.112.113.114');

        // Set session ip to something different
        $this->session->set('CLIENT_REMOTE_ADDR', '123.123.123.123');

        $this->validator->setLogger($logger);

        // Don't invalidate
        $logger->expects($this->once())->method('debug');
        $this->session->expects($this->once())->method('invalidate');
        $this->validator->handleSessionValidation($this->session);
    }

    public function testInvalidatesOnInvalidUserAgent()
    {
        $logger = $this->getMockBuilder('Monolog\Logger')->setConstructorArgs(array(''))->enableArgumentCloning()->getMock();

        // Change client agent
        $this->request->server->set('HTTP_USER_AGENT', 'TESTING');

        // Set session agent to something different
        $this->session->set('CLIENT_HTTP_USER_AGENT', 'SOME OTHER USER AGENT');

        $this->validator->setLogger($logger);

        // Invalidate
        $logger->expects($this->once())->method('debug');
        $this->session->expects($this->once())->method('invalidate');
        $this->validator->handleSessionValidation($this->session);
    }

}
