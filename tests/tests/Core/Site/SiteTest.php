<?php
namespace Concrete\Tests\Core\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\FileSaver;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Site\Resolver\Resolver;
use Concrete\Core\Site\Resolver\ResolverFactory;
use Concrete\Core\Site\Resolver\StandardDriver;
use Concrete\Core\Site\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Illuminate\Filesystem\Filesystem;

class SiteTest extends \PHPUnit_Framework_TestCase
{

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

        $site = \Core::make('site');
        $site->setEntityManager($entityManager);
        $this->assertInstanceOf('Concrete\Core\Site\Service', $site);

        $retrieved = \Site::getDefault();
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

        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $configRepoStub = $this->getMockBuilder('Concrete\Core\Config\Repository\Repository')
            ->disableOriginalConstructor()
            ->getMock();
        $configRepoStub->expects($this->once())
            ->method('get')
            ->will($this->returnValue('Testing'));

        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, $configRepoStub, $factory);

        $new = $service->add('testing', 'Testing');
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

        $entityManager->expects($this->once())
            ->method('persist');
        $entityManager->expects($this->once())
            ->method('flush');

        $config = \Core::make('config');
        $factory = new ResolverFactory(\Core::make('app'), new StandardDriver(\Core::make('Concrete\Core\Site\Factory')));
        $service = new Service($entityManager, $config, $factory);
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

        $service = \Core::make('site');
        $service->setEntityManager($entityManager);
        $retrieved = $service->getCurrentSite();

        $this->assertEquals($default, $retrieved);
    }

}
