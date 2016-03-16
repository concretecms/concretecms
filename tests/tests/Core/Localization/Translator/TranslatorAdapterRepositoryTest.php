<?php

namespace Concrete\Tests\Core\Localization\Translator;

use Concrete\Core\Localization\Translator\TranslatorAdapterRepository;
use Concrete\Tests\Core\Localization\Translator\Fixtures\DummyTranslatorAdapter;
use Concrete\Tests\Core\Localization\Translator\Fixtures\DummyTranslatorAdapterFactory;
use PHPUnit_Framework_TestCase;
use ReflectionObject;
use Symfony\Component\ClassLoader\MapClassLoader;

/**
 * Test for:
 * Concrete\Core\Localization\Translator\TranslatorAdapterRepository
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslationAdapterRepositoryTest extends PHPUnit_Framework_TestCase
{

    protected $repository;

    public static function setUpBeforeClass()
    {
        $loader = new MapClassLoader([
            'Concrete\\Tests\\Core\\Localization\\Translator\\Fixtures\\DummyTranslatorAdapter'
                => __DIR__ . '/fixtures/DummyTranslatorAdapter.php',
            'Concrete\\Tests\\Core\\Localization\\Translator\\Fixtures\\DummyTranslatorAdapterFactory'
                => __DIR__ . '/fixtures/DummyTranslatorAdapterFactory.php',
        ]);
        $loader->register();
    }

    protected function setUp()
    {
        $factory = new DummyTranslatorAdapterFactory();
        $this->repository = new TranslatorAdapterRepository($factory);
    }

    public function testRegisterTranslatorAdapter()
    {
        $adapter = new DummyTranslatorAdapter();

        $this->repository->registerTranslatorAdapter('test', 'en_US', $adapter);

        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertFalse($this->repository->hasTranslatorAdapter('test', 'tlh_US'));
        $this->assertFalse($this->repository->hasTranslatorAdapter('other', 'en_US'));
        $this->assertEquals(1, count($this->getRegisteredAdapters()));

        $this->assertEquals($adapter, $this->repository->getTranslatorAdapter('test', 'en_US'));
    }

    public function testAutoRegisterTranslatorAdapter()
    {
        $adapter = $this->repository->getTranslatorAdapter('test', 'en_US');

        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertInstanceOf('Concrete\Tests\Core\Localization\Translator\Fixtures\DummyTranslatorAdapter', $adapter);
    }

    public function testRemoveTranslatorAdapter()
    {
        $adapter = new DummyTranslatorAdapter();
        $this->repository->registerTranslatorAdapter('test', 'en_US', $adapter);

        $this->repository->removeTranslatorAdapter('test', 'en_US');

        $this->assertFalse($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertEquals(0, count($this->getRegisteredAdapters()));
    }

    public function testRegisterMultipleTranslatorAdapters()
    {
        $adapter1 = new DummyTranslatorAdapter();
        $adapter2 = new DummyTranslatorAdapter();
        $adapter3 = new DummyTranslatorAdapter();

        $this->repository->registerTranslatorAdapter('test', 'en_US', $adapter1);
        $this->repository->registerTranslatorAdapter('test', 'tlh_US', $adapter2);
        $this->repository->registerTranslatorAdapter('other', 'en_US', $adapter3);

        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'tlh_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('other', 'en_US'));
        $this->assertEquals(3, count($this->getRegisteredAdapters()));

        $this->assertEquals($adapter1, $this->repository->getTranslatorAdapter('test', 'en_US'));
        $this->assertEquals($adapter2, $this->repository->getTranslatorAdapter('test', 'tlh_US'));
        $this->assertEquals($adapter3, $this->repository->getTranslatorAdapter('other', 'en_US'));
    }

    public function testAutoRegisterMultipleTranslatorAdapters()
    {
        $adapter1 = $this->repository->getTranslatorAdapter('test', 'en_US');
        $adapter2 = $this->repository->getTranslatorAdapter('test', 'tlh_US');
        $adapter3 = $this->repository->getTranslatorAdapter('other', 'en_US');

        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'tlh_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('other', 'en_US'));
        $this->assertEquals(3, count($this->getRegisteredAdapters()));

        $this->assertInstanceOf('Concrete\Tests\Core\Localization\Translator\Fixtures\DummyTranslatorAdapter', $adapter1);
        $this->assertInstanceOf('Concrete\Tests\Core\Localization\Translator\Fixtures\DummyTranslatorAdapter', $adapter2);
        $this->assertInstanceOf('Concrete\Tests\Core\Localization\Translator\Fixtures\DummyTranslatorAdapter', $adapter3);
    }

    public function testRemoveMultipleTranslatorAdapters()
    {
        $adapter1 = new DummyTranslatorAdapter();
        $adapter2 = new DummyTranslatorAdapter();
        $adapter3 = new DummyTranslatorAdapter();

        $this->repository->registerTranslatorAdapter('test', 'en_US', $adapter1);
        $this->repository->registerTranslatorAdapter('test', 'tlh_US', $adapter2);
        $this->repository->registerTranslatorAdapter('other', 'en_US', $adapter3);

        $this->repository->removeTranslatorAdapter('test', 'en_US');

        $this->assertFalse($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('test', 'tlh_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('other', 'en_US'));
        $this->assertEquals(2, count($this->getRegisteredAdapters()));

        $this->assertNull($this->getTranslatorAdapterDirectly('test', 'en_US'));
        $this->assertNotNull($this->getTranslatorAdapterDirectly('test', 'tlh_US'));
        $this->assertNotNull($this->getTranslatorAdapterDirectly('other', 'en_US'));

        $this->repository->removeTranslatorAdapter('test', 'tlh_US');

        $this->assertFalse($this->repository->hasTranslatorAdapter('test', 'tlh_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('other', 'en_US'));
        $this->assertEquals(1, count($this->getRegisteredAdapters()));

        $this->assertNull($this->getTranslatorAdapterDirectly('test', 'tlh_US'));
        $this->assertNotNull($this->getTranslatorAdapterDirectly('other', 'en_US'));

        $this->repository->removeTranslatorAdapter('other', 'en_US');

        $this->assertFalse($this->repository->hasTranslatorAdapter('other', 'en_US'));
        $this->assertEquals(0, count($this->getRegisteredAdapters()));

        $this->assertNull($this->getTranslatorAdapterDirectly('other', 'en_US'));
    }

    public function testRemoveMultipleTranslatorAdaptersWithHandle()
    {
        $adapter1 = new DummyTranslatorAdapter();
        $adapter2 = new DummyTranslatorAdapter();
        $adapter3 = new DummyTranslatorAdapter();

        $this->repository->registerTranslatorAdapter('test', 'en_US', $adapter1);
        $this->repository->registerTranslatorAdapter('test', 'tlh_US', $adapter2);
        $this->repository->registerTranslatorAdapter('other', 'en_US', $adapter3);

        $this->repository->removeTranslatorAdaptersWithHandle('test');

        $this->assertFalse($this->repository->hasTranslatorAdapter('test', 'en_US'));
        $this->assertFalse($this->repository->hasTranslatorAdapter('test', 'tlh_US'));
        $this->assertTrue($this->repository->hasTranslatorAdapter('other', 'en_US'));

        $this->repository->removeTranslatorAdaptersWithHandle('other');

        $this->assertFalse($this->repository->hasTranslatorAdapter('other', 'tlh_US'));
        $this->assertEquals(0, count($this->getRegisteredAdapters()));
    }

    /**
     * Gets a translator adapter from the repository directly from the
     * protected array within the repository. This can be used e.g. for testing
     * that the adapters are actually removed/added to the repository's array
     * because for instace the `getTranslatorAdapter()` method in the class
     * would create a new instance if it does not already exist for the fetched
     * key.
     *
     * @param string $context
     * @param string $locale
     *
     * @return \Concrete\Core\Localization\Translator\TranslatorAdapterInterface
     */
    protected function getTranslatorAdapterDirectly($context, $locale)
    {
        $adapters = $this->getRegisteredAdapters();

        $reflection = new ReflectionObject($this->repository);
        $method = $reflection->getMethod('getKey');
        $method->setAccessible(true);
        $key = $method->invoke($this->repository, $context, $locale);
        $method->setAccessible(false);

        return array_key_exists($key, $adapters) ? $adapters[$key] : null;
    }

    /**
     * Gets the registered translator adapters array directly from the
     * protected property within the repository. This can be used in tests that
     * need information about this protected property.
     *
     * @return array
     */
    protected function getRegisteredAdapters()
    {
        $reflection = new ReflectionObject($this->repository);
        $property = $reflection->getProperty('adapters');
        $property->setAccessible(true);
        $adapters = $property->getValue($this->repository);
        $property->setAccessible(false);

        return $adapters;
    }

}
