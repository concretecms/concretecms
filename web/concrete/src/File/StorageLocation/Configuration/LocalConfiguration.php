<?php
namespace Concrete\Core\File\StorageLocation\Configuration;
use Concrete\Core\Error\Error;
use \Concrete\Flysystem\Adapter\Local;

class LocalConfiguration extends Configuration implements ConfigurationInterface
{

    protected $path;
    protected $relativePath;

    public function setRootPath($path)
    {
        $this->path = $path;
    }

    public function getRootPath()
    {
        return $this->path;
    }

    public function setWebRootRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
    }

    public function getWebRootRelativePath()
    {
        return $this->relativePath;
    }

    public function hasPublicURL()
    {
        return $this->hasRelativePath();
    }

    public function hasRelativePath()
    {
        return $this->relativePath != '';
    }

    public function getRelativePathToFile($file)
    {
        return $this->relativePath . $file;
    }

    public function getPublicURLToFile($file)
    {
        $rel = $this->getRelativePathToFile($file);
        if(strpos($rel, '://')) {
            return $rel;
        }
        return BASE_URL . $rel;
    }

    public function loadFromRequest(\Concrete\Core\Http\Request $req)
    {
        $data = $req->get('fslType');
        $this->path = rtrim($data['path'], '/');
        $this->relativePath = rtrim($data['relativePath'], '/');
    }

    /**
     * @param \Concrete\Core\Http\Request $req
     * @return Error
     */
    public function validateRequest(\Concrete\Core\Http\Request $req)
    {
        $e = new Error();
        $data = $req->get('fslType');
        $this->path = $data['path'];
        if (!$this->path) {
            $e->add(t("You must include a root path for this storage location."));
        } else if (!is_dir($this->path)) {
            $e->add(t("The specified root path does not exist."));
        } else if ($this->path == '/') {
            $e->add(t('Invalid path to file storage location. You may not choose the root directory.'));
        }
        return $e;
    }

    public function getAdapter()
    {
        $local = new Local($this->getRootPath());
        return $local;
    }
}
