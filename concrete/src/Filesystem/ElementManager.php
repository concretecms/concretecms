<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Filesystem\FileLocator\PackageLocation;
use Concrete\Core\Filesystem\FileLocator\ThemeElementLocation;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;

class ElementManager
{

    protected $registry = [];

    public function get($element)
    {
        if (isset($this->registry[$element])) {
            return $this->build($this->registry[$element]);
        }

        $class = new \ReflectionClass(Element::class);
        return $class->newInstanceArgs(func_get_args());
    }

    protected function build($o) {
        if ($o instanceof \Closure) {
            return $o();
        } else {
            return $o;
        }
    }

    public function register($element, $object)
    {
        $this->registry[$element] = $object;
    }

    public function unregister($element)
    {
        unset($this->registry[$element]);
    }

}
