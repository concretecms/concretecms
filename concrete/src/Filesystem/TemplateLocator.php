<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Foundation\Environment;

class TemplateLocator
{

    protected $locations = [];

    /**
     * Adding a location tells our locator to look in an additional spot.
     * @param $location
     * @param null $pkgHandle
     */
    public function addLocation($location, $pkgHandle = null)
    {
        $this->locations[] = [$location, $pkgHandle];
    }

    /**
     * Adds a new place to look at the front of the line
     * @param $location
     * @param null $pkgHandle
     */
    public function prependLocation($location, $pkgHandle = null)
    {
        array_unshift($this->locations, [$location, $pkgHandle]);
    }

    public function getFile()
    {
        return $this->getLocation()->getFile();
    }

    public function getLocation()
    {
        $r = Environment::get();
        $record = false;
        foreach($this->locations as $location) {
            $record = $r->getRecord($location[0], $location[1]);
            if ($record->exists()) {
                return $record;
            }
        }

        // Return the final record even if the file doesn't exist
        return $record;
    }
}
