<?php

namespace Concrete\Tests\Core\Localization\Translator\Translation;

use Concrete\Core\Localization\Translator\Translation\TranslationLoaderRepository;
use Concrete\Tests\Core\Localization\Translator\Translation\Fixtures\DummyTranslationLoader;
use PHPUnit_Framework_TestCase;
use Symfony\Component\ClassLoader\MapClassLoader;

/**
 * Tests for:
 * Concrete\Core\Localization\Translator\Translation\TranslationLoaderRepository
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class TranslationLoaderRepositoryTest extends PHPUnit_Framework_TestCase
{

    protected $repository;

    public static function setUpBeforeClass()
    {
        $loader = new MapClassLoader([
            'Concrete\\Tests\\Core\\Localization\\Translator\\Translation\\Fixtures\\DummyTranslationLoader'
                => __DIR__ . '/fixtures/DummyTranslationLoader.php'
        ]);
        $loader->register();
    }

    protected function setUp()
    {
        $this->repository = new TranslationLoaderRepository();
    }

    public function testRegisterTranslationLoader()
    {
        $loader = new DummyTranslationLoader();
        $this->repository->registerTranslationLoader('loader', $loader);

        $this->assertTrue($this->repository->hasTranslationLoader('loader'));
        $this->assertFalse($this->repository->hasTranslationLoader('other'));

        $this->assertEquals($loader, $this->repository->getTranslationLoader('loader'));
        $this->assertEquals(1, count($this->repository->getTranslationLoaders()));
    }

    public function testRemoveTranslationLoader()
    {
        $loader = new DummyTranslationLoader();
        $this->repository->registerTranslationLoader('loader', $loader);

        $this->repository->removeTranslationLoader('loader');

        $this->assertFalse($this->repository->hasTranslationLoader('loader'));
        $this->assertEquals(0, count($this->repository->getTranslationLoaders()));
    }

    public function testRegisterMultipleTranslationLoaders()
    {
        $loader1 = new DummyTranslationLoader();
        $loader2 = new DummyTranslationLoader();

        $this->repository->registerTranslationLoader('loader', $loader1);
        $this->repository->registerTranslationLoader('other', $loader2);

        $this->assertTrue($this->repository->hasTranslationLoader('loader'));
        $this->assertTrue($this->repository->hasTranslationLoader('other'));

        $this->assertEquals($loader1, $this->repository->getTranslationLoader('loader'));
        $this->assertEquals($loader2, $this->repository->getTranslationLoader('other'));

        $this->assertEquals(2, count($this->repository->getTranslationLoaders()));
    }

    public function testRemoveMultipleTranslationLoaders()
    {
        $loader1 = new DummyTranslationLoader();
        $loader2 = new DummyTranslationLoader();

        $this->repository->registerTranslationLoader('loader', $loader1);
        $this->repository->registerTranslationLoader('other', $loader2);

        $this->repository->removeTranslationLoader('loader');

        $this->assertFalse($this->repository->hasTranslationLoader('loader'));
        $this->assertTrue($this->repository->hasTranslationLoader('other'));
        $this->assertEquals(1, count($this->repository->getTranslationLoaders()));

        $this->repository->removeTranslationLoader('other');

        $this->assertFalse($this->repository->hasTranslationLoader('other'));
        $this->assertEquals(0, count($this->repository->getTranslationLoaders()));
    }

}
