<?php
namespace Concrete\Core\Routing;

class Route extends \Symfony\Component\Routing\Route
{
    public function getCallback()
    {
        $defaults = $this->getDefaults();

        return $defaults['callback'];
    }

    public function getPath()
    {
        if ($path = parent::getPath()) {
            return $path;
        }
        $defaults = $this->getDefaults();

        return $defaults['path'];
    }
}
