<?php

use Concrete\Core\Config\LoaderInterface;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Config\SaverInterface;
use Illuminate\Filesystem\Filesystem;

class LiaisonTest extends PHPUnit_Framework_TestCase
{
    /** @var Liaison */
    protected $liaison;

    /** @var Repository */
    protected $repository;

    public function setUp()
    {
        $files = new Filesystem();

        $loader = new LiaisonLoader($files);
        $saver = new LiaisonSaver($files);

        $this->repository = new Repository($loader, $saver, 'test');
        $this->liaison = new Liaison($this->repository, 'default');
    }

    public function testSet()
    {
        $this->liaison->set('test.set', $this);
        $this->assertEquals($this, $this->repository->get('default::test.set'));
    }

    public function testGet()
    {
        $this->repository->set('default::test.get', $this);
        $this->assertEquals($this, $this->repository->get('default::test.get'));
        $this->assertEquals($this, $this->repository->get('default::test.cantget', $this));
    }

    public function testSave()
    {
        $this->liaison->save('test.save', $this);

        $this->assertEquals('default::test.save', $this->repository->getSaver()->saved);
    }

    public function testHas()
    {
        $this->repository->set('default::test.has', true);

        $this->assertTrue($this->liaison->has('test.has'));
        $this->assertFalse($this->liaison->has('test.hasnt'));
    }
}

class LiaisonLoader implements LoaderInterface
{
    public function addNamespace($namespace, $hint)
    {
        return;
    }

    public function cascadePackage($environment, $package, $group, $items)
    {
        return $items;
    }

    public function clearNamespace($namespace)
    {
        return;
    }

    public function load($environment, $group, $namespace = null)
    {
        return array();
    }

    public function exists($group, $namespace = null)
    {
        return true;
    }

    public function getNamespaces()
    {
        return array();
    }
}

class LiaisonSaver implements SaverInterface
{
    public $saved = false;

    public function save($item, $value, $environment, $group, $namespace = null)
    {
        $this->saved = "{$namespace}::{$group}.{$item}";
    }
}
