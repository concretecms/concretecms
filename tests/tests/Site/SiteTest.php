<?php

namespace Concrete\Tests\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Concrete\Core\Site\Resolver\StandardDriver;
use Concrete\Core\Site\Service;
use Concrete\Theme\Elemental\PageTheme;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit_Framework_TestCase;
use Stash\Driver\Ephemeral;
use Stash\Pool;

class SiteTest extends PHPUnit_Framework_TestCase
{
    public function testService()
    {
        $service = \Core::make('site');
        $this->assertInstanceOf('Concrete\Core\Site\Service', $service);
    }

    public function testGetDefault()
    {
        // First, mock the object to be used in the test
        $default = $this->getMockBuilder(Site::class)
            ->disableOriginalConstructor()
            ->getMock();
        $default->expects($this->once())
            ->method('isDefault')
            ->will($this->returnValue(true));

        // Now, mock the repository
        $repository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($default));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $config = \Core::make('config');
        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, \Core::make('app'), $config, $factory);

        $retrieved = $service->getDefault();
        $this->assertInstanceOf('Concrete\Core\Entity\Site\Site', $retrieved);
        $this->assertEquals($default, $retrieved);
        $this->assertTrue($default->isDefault());
    }

    public function testAdd()
    {
        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->any())
            ->method('persist');
        $entityManager->expects($this->any())
            ->method('flush');

        $configRepoStub = $this->getMockBuilder('Concrete\Core\Config\Repository\Repository')
            ->disableOriginalConstructor()
            ->getMock();
        $configRepoStub->expects($this->once())
            ->method('get')
            ->will($this->returnValue('Testing'));

        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, \Core::make('app'), $configRepoStub, $factory);
        $type = new Type();
        $theme = new PageTheme();

        $new = $service->add($type, $theme, 'testing', 'Testing', 'en_US');
        $this->assertInstanceOf('Concrete\Core\Entity\Site\Site', $new);
        $this->assertEquals('testing', $new->getSiteHandle());
        $this->assertEquals('Testing', $new->getSiteName());
        $this->assertFalse($new->isDefault());
    }

    public function testInstall()
    {
        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->any())
            ->method('persist');
        $entityManager->expects($this->any())
            ->method('flush');

        $type = new Type();

        $cache = $this->getMockBuilder(RequestCache::class)
            ->getMock();

        $type_service = $this
            ->getMockBuilder(\Concrete\Core\Site\Type\Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $type_service->expects($this->once())
            ->method('getDefault')
            ->will($this->returnValue($type));

        $app = $this
            ->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $app->expects($this->any())
            ->method('make')
            ->will($this->returnValueMap([
                ['site/type', [], $type_service],
                ['cache/request', [], $cache],
            ]));

        $config = \Core::make('config');
        $factory = new ResolverFactory($app, new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, $app, $config, $factory);
        $default = $service->installDefault();

        $this->assertInstanceOf('Concrete\Core\Entity\Site\Site', $default);
        $this->assertEquals('concrete5', $default->getSiteName());
        $this->assertEquals('default', $default->getSiteHandle());
        $this->assertTrue($default->isDefault());
    }

    public function testCurrentSite()
    {
        // First, mock the object to be used in the test
        $default = $this->getMockBuilder(Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Now, mock the repository
        $repository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($default));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $config = \Core::make('config');
        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, \Core::make('app'), $config, $factory);
        $cache = new Pool();
        $cache->setDriver(new Ephemeral());
        $service->setCache($cache);
        $retrieved = $service->getSite();

        $this->assertEquals($default, $retrieved);
    }
}
