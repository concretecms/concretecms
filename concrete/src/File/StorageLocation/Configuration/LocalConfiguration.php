<?php
namespace Concrete\Core\File\StorageLocation\Configuration;

use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Support\Facade\Application;
use League\Flysystem\Adapter\Local;

class LocalConfiguration extends Configuration implements ConfigurationInterface, DeferredConfigurationInterface
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
        if (strpos($rel, '://')) {
            return $rel;
        }

        $url = \Core::getApplicationURL(true);
        $url = $url->setPath($rel);

        return rtrim((string) $url, '/');
    }

    public function loadFromRequest(\Concrete\Core\Http\Request $req)
    {
        $data = $req->get('fslType');
        $this->path = rtrim($data['path'], '/');
        if (isset($data['relativePath'])) {
            $this->relativePath = rtrim($data['relativePath'], '/');
        }
    }

    /**
     * @param \Concrete\Core\Http\Request $req
     *
     * @return Error
     */
    public function validateRequest(\Concrete\Core\Http\Request $req)
    {
        $app = Application::getFacadeApplication();
        $e = $app->make('error');
        $data = $req->get('fslType');
        $fslID = $req->get('fslID');
        $locationHasFiles = false;
        $locationRootPath = null;
        if (!empty($fslID)) {
            $location = $app->make(StorageLocationFactory::class)->fetchByID($fslID);
            if (is_object($location)) {
                $locationHasFiles = $location->hasFiles();
                $locationRootPath = $location->getConfigurationObject()->getRootPath();
            }
        }
        $this->path = $data['path'];
        if (!$this->path) {
            $e->add(t("You must include a root path for this storage location."));
        } elseif (stripos($this->path,'phar:') !== false) {
            $e->add(t("Invalid path to file storage location."));
        } elseif (!is_dir($this->path)) {
            $e->add(t("The specified root path does not exist."));
        } elseif ($this->path == '/') {
            $e->add(t('Invalid path to file storage location. You may not choose the root directory.'));
        } elseif ($locationHasFiles && $locationRootPath !== $this->path) {
            $e->add(t('You can not change the root path of this storage location because it contains files.'));
        }

        return $e;
    }

    public function getAdapter()
    {
        $local = new Local($this->getRootPath());

        return $local;
    }
}
