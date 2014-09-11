<?php
namespace Concrete\Core\Config;

use Illuminate\Filesystem\Filesystem;

class FileSaver implements SaverInterface
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function save($item, $value, $environment, $group, $namespace = null)
    {
        $path = DIR_APPLICATION . '/config/generated_overrides';

        if (!$this->files->exists($path)) {
            $this->files->makeDirectory($path, 0777);
        } elseif (!$this->files->isDirectory($path)) {
            $this->files->delete($path);
            $this->files->makeDirectory($path, 0777);
        }

        if ($namespace) {
            $path = "{$path}/{$namespace}";

            if (!$this->files->exists($path)) {
                $this->files->makeDirectory($path, 0777);
            } elseif (!$this->files->isDirectory($path)) {
                $this->files->delete($path);
                $this->files->makeDirectory($path, 0777);
            }
        }

        $file = "{$path}/{$group}.php";

        $current = array();
        if ($this->files->exists($file)) {
            $current = $this->files->getRequire($file);
        }

        array_set($current, $item, $value);

        $renderer = new Renderer($current);
        return $this->files->put($file, $renderer->render()) !== false;

    }

}
