<?php

namespace Concrete\Tests\Database;

use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * DatabaseServiceProviderTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
#[Group('orm_setup')]
class DatabaseServiceProviderTest extends TestCase
{
    protected $app;

    public function setUp():void
    {
        parent::setUp();
        $this->app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    }

    /**
     * Test if the the classes are mapped correctly be the IoC during the bootstrap.
     *
     * @param string $ioCAlias
     * @param string $expected
     * @param string $message
     */
    #[DataProvider('dataProviderForTestDoctrineORMSetup')]
    public function testDoctrineORMSetup($ioCAlias, $expected, $message)
    {
        $doctrineConfig = $this->app->make($ioCAlias);
        $this->assertInstanceOf($expected, $doctrineConfig, $message);
    }

    public static function dataProviderForTestDoctrineORMSetup()
    {
        return [
            ['Doctrine\ORM\Configuration', 'Doctrine\ORM\Configuration', 'This is not an instance of Doctrine\ORM\Configuration.'],
            ['Doctrine\Common\Cache\ArrayCache', 'Symfony\Component\Cache\DoctrineProvider', 'This is not an instance of Symfony\Component\Cache\DoctrineProvider'],
            ['Doctrine\Common\Annotations\AnnotationReader', 'Doctrine\Common\Annotations\AnnotationReader', 'This is not an instance of Doctrine\Common\Annotations\AnnotationReader'],
            ['Doctrine\Common\Annotations\SimpleAnnotationReader', 'Doctrine\Common\Annotations\SimpleAnnotationReader', 'This is not an instance of Doctrine\Common\Annotations\SimpleAnnotationReader'],
            ['Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain', 'Doctrine\Persistence\Mapping\Driver\MappingDriverChain', 'This is not an instance of Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain'],
            ['orm/cachedAnnotationReader', 'Doctrine\Common\Annotations\CachedReader', 'This is not an instance of Doctrine\Common\Annotations\CachedReader'],
            ['orm/cachedSimpleAnnotationReader', 'Doctrine\Common\Annotations\CachedReader', 'This is not an instance of Doctrine\Common\Annotations\CachedReader'],
        ];
    }

    /**
     * Test the ORM cache settings for the differnet invironments (devlop & production).
     *
     * @todo Doesn't work yet properly. the line whiche overrides the config instance makes crache the succeeding tests.
     *
     * @param string $ioCAlias
     * @param string $compare
     * @param string $isDevMode
     */
//    #[DataProvider('dataProviderForTestORMCache')]
//    public function testORMCacheSettings($ioCAlias, $compare, $isDevMode){
//
//        // Store something in the config file and fetching it doesn't work.
//        // The value has to be store first, then a new config file repository
//        // has to be generated.
//        //$config = $this->getTestFileConfig();
//        $config = $this->app->make('config');
//        $config->save('concrete.cache.doctrine_dev_mode', $isDevMode);
//
//        // override the application config
//        //$this->app->instance('config', $config);
//
//        // Does the stored value is equals the loaded value
//        $configLoaded = $this->app->make('config')->get('concrete.cache.doctrine_dev_mode');
//        $this->assertEquals($isDevMode, $configLoaded, 'The stored config and the loaded config value are not the same.');
//        // Does the right caching is loaded
//        $doctrineConfig = $this->app->make($ioCAlias);
//        $this->assertInstanceOf($compare, $doctrineConfig);
//    }

    public static function dataProviderForTestORMCache()
    {
        return [
            'ORM development cache settings' => ['orm/cache', 'Doctrine\Common\Cache\ArrayCache', true],
            'ORM production cache settings' => ['orm/cache', 'Concrete\Core\Cache\Adapter\DoctrineCacheDriver', false],
        ];
    }

    /**
     * Test if the interface and the concrete EntityManagerConfigFactory are mapped correctly.
     *
     * @param string $ioCAlias
     * @param string $expected1
     * @param string $expected2
     */
    #[DataProvider('dataProviderForTestEntityManagerConfigFactory')]
    public function testEntityManagerConfigFactory($ioCAlias, $expected1, $expected2)
    {
        $entityManagerConfigFactory = $this->app->make($ioCAlias);
        $this->assertInstanceOf($expected1, $entityManagerConfigFactory);
        $this->assertInstanceOf($expected2, $entityManagerConfigFactory);
    }

    public static function dataProviderForTestEntityManagerConfigFactory()
    {
        return [
            'Map EntityManagerConfigFactoryInterface' => ['Concrete\Core\Database\EntityManagerConfigFactoryInterface', 'Concrete\Core\Database\EntityManagerConfigFactoryInterface', 'Concrete\Core\Database\EntityManagerConfigFactory'],
            'Map EntityManagerConfigFactory' => ['Concrete\Core\Database\EntityManagerConfigFactory', 'Concrete\Core\Database\EntityManagerConfigFactory', 'Concrete\Core\Database\EntityManagerConfigFactoryInterface'],
        ];
    }

    /**
     * Test if the interface and the concrete EntityManagerFactory are mapped correctly.
     *
     * @param string $ioCAlias
     * @param string $expected1
     * @param string $expected2
     */
    #[DataProvider('dataProviderForTestEntityManagerFactory')]
    public function testEntityManagerFactory($ioCAlias, $expected1, $expected2)
    {
        $entityManagerFactory = $this->app->make($ioCAlias);
        $this->assertInstanceOf($expected1, $entityManagerFactory);
        $this->assertInstanceOf($expected2, $entityManagerFactory);
    }

    public static function dataProviderForTestEntityManagerFactory()
    {
        return [
            'Map EntityManagerFactoryInterface' => ['Concrete\Core\Database\EntityManagerFactoryInterface', 'Concrete\Core\Database\EntityManagerFactoryInterface', 'Concrete\Core\Database\EntityManagerFactory'],
            'Map EntityManagerFactory' => ['Concrete\Core\Database\EntityManagerFactory', 'Concrete\Core\Database\EntityManagerFactory', 'Concrete\Core\Database\EntityManagerFactoryInterface'],
        ];
    }

    /**
     * Test if the Doctrine DBAL connect, database connection and the
     * database managers are mapped correctly.
     *
     * @param string $ioCAlias
     * @param string $expected1
     */
    #[DataProvider('dataProviderForTestDatabaseAndDBALSetup')]
    public function testDatabaseAndDBALSetup($ioCAlias, $expected1)
    {
        $instance = $this->app->make($ioCAlias);
        $this->assertInstanceOf($expected1, $instance);
    }

    public static function dataProviderForTestDatabaseAndDBALSetup()
    {
        return [
            ['Concrete\Core\Database\DatabaseManager', 'Concrete\Core\Database\DatabaseManager'],
            ['database', 'Concrete\Core\Database\DatabaseManager'],
            ['Concrete\Core\Database\DatabaseManagerORM', 'Concrete\Core\Database\DatabaseManagerORM'],
            ['database/orm', 'Concrete\Core\Database\DatabaseManagerORM'],
        ];
    }

    /**
     * Create new config file repository.
     *
     * @return \Concrete\Core\Config\Repository\Repository
     */
    protected function getTestFileConfig()
    {
        $defaultEnv = \Config::getEnvironment();
        $fileSystem = new \Illuminate\Filesystem\Filesystem();
        $fileLoader = new \Concrete\Core\Config\FileLoader($fileSystem);
        $directFileSaver = new \Concrete\Core\Config\FileSaver($fileSystem);
        $repository = new \Concrete\Core\Config\Repository\Repository($fileLoader,
            $directFileSaver, $defaultEnv);

        return $repository;
    }
}
