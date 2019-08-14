<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Foundation\Environment;

/**
 * @since 8.0.0
 */
class TemplateLocator
{

    /**
     * @since 8.2.0
     */
    protected $template;

    /**
     * @since 8.2.0
     */
    protected function createTemplateFromInput($input)
    {
        $template = null;
        if (!($input instanceof Template)) {
            if (is_array($input)) {
                $template = new Template($input[0], $input[1]);
            } else if (is_string($input)) {
                $template = new Template($input);
            }
        } else {
            $template = $input;
        }

        return $template;
    }

    /**
     * @since 8.2.0
     */
    protected function createLocationFromInput($input)
    {
        $location = null;
        if (!($input instanceof TemplateLocation)) {
            if (is_array($input)) {
                $location = new TemplateLocation($input[0], $input[1]);
            } else if (is_string($input)) {
                $location = new TemplateLocation($input);
            }
        } else {
            $location = $input;
        }
        return $location;
    }


    /**
     * @since 8.2.0
     */
    public function __construct($template = null)
    {
        if ($template) {
            $this->setTemplate($template);
        }
    }

    /**
     * @return Template
     * @since 8.2.0
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @since 8.2.0
     */
    public function setTemplate($template)
    {
        $this->template = $this->createTemplateFromInput($template);
    }


    protected $locations = [];

    /**
     * Adding a location tells our locator to look in an additional spot.
     * @param $location
     */
    public function addLocation($location)
    {
        $this->locations[] = $this->createLocationFromInput($location);
    }

    /**
     * Adds a new place to look at the front of the line
     * @param $location
     */
    public function prependLocation($location)
    {
        array_unshift($this->locations, $this->createLocationFromInput($location));
    }

    /**
     * @since 8.2.0
     */
    protected function getPath(TemplateLocation $location, Template $template)
    {
        $location = $location->getLocation()
            . '/'
            . $template->getTemplateHandle() . '.php';
        return $location;
    }

    public function getFile()
    {
        $location = $this->getLocation();
        if ($location) {
            return $location->getFile();
        }
     }

    /**
     * @return array
     * @since 8.2.0
     */
    public function getLocations()
    {
        return $this->locations;
    }

    public function getLocation()
    {
        $r = Environment::get();
        $record = false;
        foreach($this->locations as $location) {
            $pkgHandle = null;
            if ($this->template) {
                $path = $this->getPath($location, $this->template);
                if ($this->template->getPackageHandle()) {
                    $pkgHandle = $this->template->getPackageHandle();
                }
            } else {
                $path = $location->getLocation();
            }
            if (!$pkgHandle) {
                $pkgHandle = $location->getPackageHandle();
            }

            $record = $r->getRecord($path, $pkgHandle);
            if ($record->exists()) {
                return $record;
            }
        }


        // Return the final record even if the file doesn't exist
        return $record;
    }
}
