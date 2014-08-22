<?php
namespace Concrete\Core\Config;

use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class ConfigLoader extends FileLoader {

    protected $config;

    public function __construct(Filesystem $files, Config $config) {
        $this->config = $config;
        parent::__construct($files, DIR_APPLICATION . '/config');
        $this->addNamespace('core', DIR_BASE . '/concrete/config');
    }

    public function load($environment, $group, $namespace = null)
    {
        if (is_null($namespace)) {
            return array_replace_recursive(
                parent::load($environment, $group, 'core'),
                parent::load($environment, $group, $namespace)
            );
        }
        return parent::load($environment, $group, $namespace);
    }

}
