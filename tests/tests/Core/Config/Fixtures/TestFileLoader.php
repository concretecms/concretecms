<?php
namespace Concrete\Tests\Core\Config\Fixtures;

use Concrete\Core\Config\FileLoader;

class TestFileLoader extends FileLoader
{

    /**
     * TestFileLoader constructor.
     */
    public function __construct($files)
    {
        parent::__construct($files);
        $this->defaultPath = DIR_TESTS . "/config";
    }

}
