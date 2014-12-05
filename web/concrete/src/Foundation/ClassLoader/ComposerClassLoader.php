<?php
namespace Concrete\Core\Foundation\ClassLoader;
use Illuminate\Filesystem\Filesystem;

class ComposerClassLoader
{

    /** @var string */
    protected $vendorPath;

    /** @var \Composer\Autoload\ClassLoader */
    protected $loader;

    /** @var FileSystem */
    protected $fileSystem;

    public function __construct($vendor_path)
    {
        $this->vendorPath = $vendor_path;
    }

    public function register()
    {
        $autoload_script = $this->getVendorPath() . '/autoload.php';
        $fs = $this->getFileSystem();

        if ($fs->exists($autoload_script)) {
            $this->loader = $fs->getRequire($autoload_script);
            return !!$this->loader instanceof;
        } else {
            throw new ComposerNotFoundException();
        }

        return false;
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem()
    {
        if (!$this->fileSystem) {
            $this->fileSystem = new Filesystem();
        }
        return $this->fileSystem;
    }

    /**
     * @param $fileSystem
     */
    public function setFileSystem(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return string
     */
    public function getVendorPath()
    {
        return $this->vendorPath;
    }

}
