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

        $ns_string = 'null';
        if ($namespace) {
            $ns_string = $namespace;
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
            if (\Config::get('concrete.config.temp_save', true)) {
                // Make sure that we miss cache.
                $temp_file = tempnam($path, $group . '_');
                $contents = $this->files->get($file);
                $this->files->put($temp_file, $contents);

                $current = $this->files->getRequire($temp_file);
                $this->files->delete($temp_file);
            } else {
                $current = $this->files->getRequire($file);
            }
        }

        array_set($current, $item, $value);

        $renderer = new Renderer($current);

        $header = array(
            "<?php",
            "",
            "/**",
            " * -----------------------------------------------------------------------------",
            " * Generated " . date(DATE_ATOM),
            " *",
            " * @item      {$item}",
            " * @group     {$group}",
            " * @namespace {$ns_string}",
            " * -----------------------------------------------------------------------------",
            " */",
            "return "
        );


        $rendered = $renderer->render(PHP_EOL, '    ', implode(PHP_EOL, $header));
        return $this->files->put($file, $rendered) !== false;

    }

}
