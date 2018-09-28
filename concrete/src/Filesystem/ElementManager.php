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
            $o = $this->registry[$element];
            if ($o instanceof \Closure) {
                $element = $o();
            } else {
                $element = $o;
            }
            $element->populateFromArguments(func_get_args());
            return $element;
        } else {
            $class = new \ReflectionClass(Element::class);
            return $class->newInstanceArgs(func_get_args());
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
