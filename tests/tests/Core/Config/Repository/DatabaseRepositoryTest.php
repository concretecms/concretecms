<?php
use Concrete\Core\Config\DatabaseLoader;
use Concrete\Core\Config\DatabaseSaver;
use Concrete\Core\Config\Repository\Repository;

class DatabaseRepositoryTest extends \ConcreteDatabaseTestCase
{

    /** @var Repository */
    protected $repository;

    protected $tables = array('Config');

    public function setUp()
    {
        $this->repository = new Repository(new DatabaseLoader(), new DatabaseSaver(), 'test');
    }

    public function testSave()
    {
        $group = md5(uniqid());
        $item = 'test.item';
        $key = "{$group}.{$item}";

        $this->repository->save($key, $group);
        $this->repository->clearCache();
        $this->assertEquals($group, $this->repository->get($key, false));
    }

    public function testSaveNamespace()
    {
        $namespace = md5(uniqid());
        $group = md5(uniqid());
        $item = 'test.item';
        $key = "{$namespace}::{$group}.{$item}";

        $this->repository->save($key, $namespace);
        $this->repository->clearCache();
        $this->assertEquals($namespace, $this->repository->get($key, false));
    }

}
