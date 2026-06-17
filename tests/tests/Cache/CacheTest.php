<?php

namespace Concrete\Tests\Cache;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Application\Application;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;

class CacheTest extends TestCase
{
    /** @var array */
    private $original;

    /** @var \Concrete\Core\Cache\Level\RequestCache */
    private $requestCache;

    /** @var \Concrete\Core\Cache\Level\ExpensiveCache */
    private $expensiveCache;

    /** @var \Concrete\Core\Cache\Level\ObjectCache */
    private $cache;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * @before
     */
    public function beforeEach(): void
    {
        $app = ApplicationFacade::getFacadeApplication();
        $this->original = [
            'cache/request' => $app->make('cache/request'),
            'cache/expensive' => $app->make('cache/expensive'),
            'cache' => $app->make('cache'),
            EntityManagerInterface::class => $app->make(EntityManagerInterface::class),
        ];

        $this->requestCache = $this->getMockBuilder('Concrete\Core\Cache\Level\RequestCache')
            ->disableOriginalConstructor()
            ->getMock();
        $this->expensiveCache = $this->getMockBuilder('Concrete\Core\Cache\Level\ExpensiveCache')
            ->disableOriginalConstructor()
            ->getMock();
        $this->cache = $this->getMockBuilder('Concrete\Core\Cache\Level\ObjectCache')
            ->disableOriginalConstructor()
            ->getMock();
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $overrides = [
            'cache/request' => $this->requestCache,
            'cache/expensive' => $this->expensiveCache,
            'cache' => $this->cache,
            EntityManagerInterface::class => $this->em,
        ];
        foreach ($overrides as $key => $value) {
            $app->bind($key, function () use ($value) {
                return $value;
            });
        }
    }

    /**
     * @after
     */
    public function afterEach(): void
    {
        $app = ApplicationFacade::getFacadeApplication();
        foreach ($this->original as $key => $value) {
            $app->bind($key, function () use ($value) {
                return $value;
            });
        }
    }

    public function testEnableAll(): void
    {
        $mdFactory = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $mdFactory->expects($this->once())->method('setCache')->with(
            $this->isInstanceOf('Symfony\Component\Cache\Adapter\DoctrineAdapter')
        );

        $this->requestCache->expects($this->once())->method('enable');
        $this->expensiveCache->expects($this->once())->method('enable');
        $this->cache->expects($this->once())->method('enable');
        $this->em->expects($this->once())->method('getMetadataFactory')->willReturn($mdFactory);

        Cache::enableAll();
    }

    public function testEnableAllWithoutDbConnection(): void
    {
        $app = ApplicationFacade::getFacadeApplication();
        $config = $app->make('config');
        $conn = $config->get('database.default-connection');
        $config->set('database.default-connection', null);

        $this->requestCache->expects($this->once())->method('enable');
        $this->expensiveCache->expects($this->once())->method('enable');
        $this->cache->expects($this->once())->method('enable');
        $this->em->expects($this->never())->method('getMetadataFactory');

        Cache::enableAll();

        $config->set('database.default-connection', $conn);
    }

    public function testDisableAll(): void
    {
        $mdFactory = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $mdFactory->expects($this->once())->method('setCache')->with(
            $this->isInstanceOf('Symfony\Component\Cache\Adapter\DoctrineAdapter')
        );

        $this->requestCache->expects($this->once())->method('disable');
        $this->expensiveCache->expects($this->once())->method('disable');
        $this->cache->expects($this->once())->method('disable');
        $this->em->expects($this->once())->method('getMetadataFactory')->willReturn($mdFactory);

        Cache::disableAll();
    }

    public function testDisableAllWithoutDbConnection(): void
    {
        $app = ApplicationFacade::getFacadeApplication();
        $config = $app->make('config');
        $conn = $config->get('database.default-connection');
        $config->set('database.default-connection', null);

        $this->requestCache->expects($this->once())->method('disable');
        $this->expensiveCache->expects($this->once())->method('disable');
        $this->cache->expects($this->once())->method('disable');
        $this->em->expects($this->never())->method('getMetadataFactory');

        Cache::disableAll();

        $config->set('database.default-connection', $conn);
    }
}
