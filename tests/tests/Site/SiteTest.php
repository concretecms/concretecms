<?php

namespace Concrete\Tests\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Concrete\Core\Site\Resolver\StandardDriver;
use Concrete\Core\Site\Service;
use Concrete\Core\Site\Type\Controller\Manager;
use Concrete\Core\Site\Type\Controller\StandardController;
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
        $singletons = [
            'site' => \Concrete\Core\Site\Service::class,
            'site/type' => \Concrete\Core\Site\Type\Service::class,
        ];
        foreach ($singletons as $alias => $implementation) {
            $this->assertInstanceOf($implementation, \Core::make($alias));
            $this->assertSame(\Core::make($alias), \Core::make($alias), 'Making the alias should always return the same instance');
            $this->assertSame(\Core::make($implementation), \Core::make($implementation), 'Making the implementation should always return the same instance');
            $this->assertSame(\Core::make($alias), \Core::make($implementation), 'Making the alias and the implementation should return the same instance');
        }
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

        $siteTypeService = $this->getMockBuilder(\Concrete\Core\Site\Type\Service::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = \Core::make('config');
        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, \Core::make('app'), $config, $factory, $siteTypeService);

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

        $skeletonService = $this->getMockBuilder(\Concrete\Core\Site\Type\Skeleton\Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $groupService = $this->getMockBuilder(\Concrete\Core\Site\User\Group\Service::class)
            ->disableOriginalConstructor()
            ->getMock();


        $controller = $this->getMockBuilder(StandardController::class)
            ->disableOriginalConstructor()
            ->getMock();
        $controller->expects($this->once())
            ->method('add')
            ->will($this->returnArgument(0));

        $siteTypeService = $this->getMockBuilder(\Concrete\Core\Site\Type\Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $siteTypeService->expects($this->once())
            ->method('getController')
            ->will($this->returnValue($controller));

        $siteTypeService->expects($this->once())
            ->method('getSkeletonService')
            ->will($this->returnValue($skeletonService));
        $siteTypeService->expects($this->once())
            ->method('getGroupService')
            ->will($this->returnValue($groupService));

        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, \Core::make('app'), $configRepoStub, $factory, $siteTypeService);
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
        $service = new Service($entityManager, $app, $config, $factory, $type_service);
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

        $siteTypeService = $this->getMockBuilder(\Concrete\Core\Site\Type\Service::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config = \Core::make('config');
        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, \Core::make('app'), $config, $factory, $siteTypeService);
        $cache = new Pool();
        $cache->setDriver(new Ephemeral());
        $service->setCache($cache);
        $retrieved = $service->getSite();

        $this->assertEquals($default, $retrieved);
    }
}
