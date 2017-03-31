<?php
namespace Concrete\Core\Filesystem;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Filesystem\FileLocator\PackageLocation;
use Concrete\Core\Filesystem\FileLocator\ThemeElementLocation;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;

/**
 * Class Element
 * An object-oriented wrapper for core element functionality, with support for events, locators, controllers and elements.
 */
class Element
{

    protected $element;
    protected $variables = array();
    protected $pkgHandle;
    protected $locator;
    protected $page;
    protected $controller;
    protected $controllerArguments = array();

    /**
     * Element constructor.
     * @param $element
     */
    public function __construct($element)
    {
        $this->element = $element;
        $args = func_get_args();
        if (count($args) > 1) {
            for ($i = 1; $i < count($args); $i++) {
                $arg = $args[$i];
                if ($arg instanceof Page) {
                    $this->page = $arg;
                }
                if (is_array($arg)) {
                    $this->controllerArguments = $arg;
                }
                if (is_string($arg)) {
                    $this->pkgHandle = $arg;
                }
            }
        }
        $this->locator = $this->createLocator();
    }

    protected function getBaseLocator()
    {
        $app = Facade::getFacadeApplication();
        $fs = new \Illuminate\Filesystem\Filesystem();
        $locator = new FileLocator($fs, $app);
        return $locator;
    }

    /**
     * @return FileLocator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    protected function createLocator()
    {
        $locator = $this->getBaseLocator();
        if ($this->page) {
            $theme = $this->page->getCollectionThemeObject();
            if ($theme) {
                $locator->addLocation(new ThemeElementLocation($theme));
            }
        }
        if ($this->pkgHandle) {
            $locator->addLocation(new PackageLocation($this->pkgHandle));
        }
        return $locator;
    }

    public function getElementPath()
    {
        if ($controller = $this->getElementController()) {
            $element = $controller->getElement();
        } else {
            $element = $this->element;
        }
        return DIRNAME_ELEMENTS . DIRECTORY_SEPARATOR . $element . '.php';
    }

    public function exists()
    {
        $record = $this->locator->getRecord($this->getElementPath());
        return $record->exists();
    }

    public function set($key, $value)
    {
        $this->variables[$key] = $value;
        return $this;
    }

    /**
     * @return ElementController
     */
    public function getElementController()
    {
        if (!isset($this->controller)) {
            $path = DIRNAME_CONTROLLERS . DIRECTORY_SEPARATOR . 'element' . DIRECTORY_SEPARATOR . $this->element
                . '.php';
            $class = 'Controller\\Element';
            $segments = explode('/', $this->element);
            foreach($segments as $segment) {
                $class .= '\\' . camelcase($segment);
            }
            $class = overrideable_core_class($class, $path, $this->pkgHandle);
            if (class_exists($class)) {
                $refl = new \ReflectionClass($class);
                $this->controller = $refl->newInstanceArgs($this->controllerArguments);
                $this->controller->setPackageHandle($this->pkgHandle);
            }
        }
        return $this->controller;
    }

    public function render()
    {
        $variables = $this->variables;
        $controller = $this->getElementController();
        if ($controller) {
            $controller->view();
            $variables = array_merge($variables, $controller->getSets());
        }

        extract($variables);

        if ($this->exists()) {
            $record = $this->locator->getRecord($this->getElementPath());
            include($record->getFile());
        } else {
            throw new \RuntimeException(t('Element %s does not exist', $this->getElementPath()));
        }
    }

}
