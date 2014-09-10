<?php
namespace Concrete\Core\Config;

class Repository extends \Illuminate\Config\Repository
{

    /**
     * @var ConfigLoader
     */
    protected $loader;

    public function clear($key)
    {
        $this->set($key, null);
    }

    public function getOrSet($key, $value)
    {
        $val = $this->get($key, $this);

        if ($val === $this) {
            $this->save($key, $value);
            $val = $value;
        }

        return $val;
    }

    public function save($key, $value)
    {
        $this->set($key, $value);

        list($namespace, $group, $item) = $this->parseKey($key);
        $file_system = $this->loader->getFilesystem();
        $path = DIR_APPLICATION . '/config/generated_overrides';

        if (!$file_system->exists($path)) {
            $file_system->makeDirectory($path, 0777);
        } elseif (!$file_system->isDirectory($path)) {
            $file_system->delete($path);
            $file_system->makeDirectory($path, 0777);
        }

        $file = "{$path}/{$group}.php";
        $current = array();
        if ($file_system->exists($file)) {
            $current[$group] = $file_system->getRequire($file);
        }

        array_set($current, $key, $value);

        $renderer = new ConfigRenderer($current[$group]);
        $file_system->put($file, $renderer->render());

        parent::set($key, $value);
    }

    public function clearCache()
    {
        $this->items = array();
    }

}
