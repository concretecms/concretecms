<?
namespace Concrete\Core\File\StorageLocation\Configuration;
use \Gaufrette\Adapter\Local;

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