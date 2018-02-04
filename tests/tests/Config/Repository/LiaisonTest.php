<?php

namespace Concrete\Tests\Config\Repository;

use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Config\Repository\Repository;
use Concrete\TestHelpers\Config\Repository\LiaisonLoader;
use Concrete\TestHelpers\Config\Repository\LiaisonSaver;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;

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
