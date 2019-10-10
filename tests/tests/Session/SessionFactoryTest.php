<?php

namespace Concrete\Tests\Session;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Session\SessionFactory;
use Concrete\Core\Session\SessionFactoryInterface;
use Concrete\Core\Session\Storage\Handler\NativeFileSessionHandler;
use Concrete\Core\Session\Storage\Handler\RedisSessionHandler;
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

        $config['concrete.session'] = $this->getRedisConfig();
        $redis_handler = $method->invokeArgs($this->factory, $this->getRedisConfig());
        $this->assertInstanceOf(RedisSessionHandler::class, $redis_handler);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRedisSessionHandler() {
        // Make the private `getSessionHandler` method accessible
        $reflection = new \ReflectionClass(get_class($this->factory));
        $method = $reflection->getMethod('getSessionHandler');
        $method->setAccessible(true);

        $config['concrete.session'] = $this->getRedisConfig();
        /** @var $redis_handler  RedisSessionHandler */
        $redis_handler = $method->invokeArgs($this->factory, $this->getRedisConfig());
        $reflection = new \ReflectionClass(RedisSessionHandler::class);
        $property = $reflection->getProperty('redis');
        $property->setAccessible(true);
        $redisClass = $property->getValue($redis_handler);
        /** @var \Redis $redisClass */

        $this->assertInstanceOf(\Redis::class, $redisClass);
        $this->assertTrue($redisClass->ping());
        $redisConfig = $this->getRedisConfig(2);
        $config['concrete.session'] = $redisConfig;
        /** @var $redis_handler  RedisSessionHandler */
        $redis_handler = $method->invokeArgs($this->factory, $redisConfig);
        $property->setAccessible(true);
        /** @var  $redisClass  \RedisArray */
        $redisClass = $property->getValue($redis_handler);
        $this->assertInstanceOf(\RedisArray::class, $redisClass);
        $this->assertTrue($redisClass->ping());
        $hosts = $redisClass->_hosts();

        $this->assertSame($hosts, $this->getRedisHosts($redisConfig));

    }

    private function getRedisHosts($config)
    {
        $hosts = [];
        if (is_array($config) && isset($config['redis'])) {
            $config = $config['redis'];
            if (is_array($config['servers'])) {
                foreach ($config['servers'] as $server) {
                    if (is_array($server)) {
                        $hosts[] = $server['server'] . ':'. $server['port'];
                    }

                }
            }
        }

        return $hosts;


    }

    private function getRedisConfig($servers = 1) {
        $config = ['handler' => 'redis',
            'redis' => [
                'servers'=>[]
            ]
        ];
        if ($servers < 1) {
            $servers = 1;
        }
        $i = 0;
        while ($i < $servers) {
                $port = 6379 + $i;
                $config['redis']['servers'][] =[
                    'server' => '127.0.0.1',
                    'port' => $port,
                    'ttl' => 30,
                    'password'=>'randomredis',
                ];
            $i++;
        }
        return $config;
    }
}
