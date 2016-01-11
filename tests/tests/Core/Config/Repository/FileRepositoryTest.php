<?php

use Concrete\Core\Config\FileLoader;
use Concrete\Core\Config\FileSaver;
use Concrete\Core\Config\Repository\Repository;
use Illuminate\Filesystem\Filesystem;

class FileRepositoryTest extends \ConcreteDatabaseTestCase
{
    /** @var Filesystem */
    protected $files;

    /** @var Repository */
    protected $repository;

    protected $tables = array('Config');

    public function setUp()
    {
        $this->files = new Filesystem();
        $this->repository = new Repository(new FileLoader($this->files), new FileSaver($this->files), 'test');
    }

    public function testSave()
    {
        $group = md5(uniqid());
        $item = 'test.item';
        $key = "{$group}.{$item}";

        $this->repository->save($key, $group);
        $this->assertEquals($group, $this->repository->get($key, false));

        $this->files->delete(DIR_APPLICATION . "/config/generated_overrides/{$group}.php");
    }

    public function testSaveNamespace()
    {
        $namespace = md5(uniqid());
        $group = md5(uniqid());
        $item = 'test.item';
        $key = "{$namespace}::{$group}.{$item}";

        $this->repository->save($key, $namespace);
        $this->assertEquals($namespace, $this->repository->get($key, false));

        $this->files->deleteDirectory(DIR_APPLICATION . "/config/generated_overrides/{$namespace}/");
    }
}
