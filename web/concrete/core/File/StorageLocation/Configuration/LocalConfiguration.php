<?
namespace Concrete\Core\File\StorageLocation\Configuration;
use \League\Flysystem\Adapter\Local;

class LocalConfiguration extends Configuration
{

    protected $path;

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
        return BASE_URL . $this->getRelativePathToFile($file);
    }

    public function loadFromRequest(\Concrete\Core\Http\Request $req)
    {
        $data = $req->get('fslData');
        $this->path = $data['local']['path'];
    }

    public function getAdapter()
    {
        $local = new Local($this->getRootPath());
        return $local;
    }
}