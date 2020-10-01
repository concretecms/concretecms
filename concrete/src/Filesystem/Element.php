<?php

namespace Concrete\Core\Filesystem;

use Concrete\Core\Filesystem\FileLocator\PackageLocation;
use Concrete\Core\Filesystem\FileLocator\ThemeElementLocation;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\FileLocatorView;

/**
 * An object-oriented wrapper for core element functionality, with support for events, locators, controllers and elements.
 */
class Element implements LocatableFileInterface
{
    /**
     * The element name.
     *
     * @var string
     */
    protected $element;

    /**
     * The list of variables to be "set" in the view.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * The handle of the package defining this element.
     *
     * @var string|null
     */
    protected $pkgHandle;

    /**
     * The locator instance to be used to get the actual PHP file that implements the view.
     *
     * @var \Concrete\Core\Filesystem\FileLocator
     */
    protected $locator;

    /**
     * The page where the element will be rendered.
     *
     * @var \Concrete\Core\Page\Page|null
     */
    protected $page;

    /**
     * The element controller.
     *
     * @var \Concrete\Core\Controller\ElementController|null
     */
    protected $controller;

    /**
     * The arguments to be used when calling the constructor of the element controller.
     *
     * @var array
     */
    protected $controllerArguments = [];

    /**
     * Element constructor.
     *
     * @param string $element the element name
     * @param \Concrete\Core\Page\Page $page
     * @param array $elementArguments the arguments to be used when calling the constructor of the element controller
     * @param string $pkgHandle the handle of the package defining this element
     */
    public function __construct($element)
    {
        $this->element = $element;
        $args = func_get_args();
        $this->populateFromArguments($args);
        $this->locator = $this->createLocator();
    }

    /**
     * Initialize this instance.
     *
     * @param array $args the first element will be discarded; other arguments can be of type:
     * - Page: the page where the element will be rendered
     * - array: the arguments to be used when calling the constructor of the element controller
     * - string: the handle of the package defining this element
     */
    public function populateFromArguments($args)
    {
        $count = count($args);
        for ($i = 1; $i < $count; ++$i) {
            $arg = $args[$i];
            if ($arg instanceof Page) {
                $this->page = $arg;
            } elseif (is_array($arg)) {
                $this->controllerArguments = $arg;
                foreach ($arg as $key => $value) {
                    $this->set($key, $value);
                }
            } elseif (is_string($arg)) {
                $this->pkgHandle = $arg;
            }
        }
    }

    /**
     * Get the locator instance to be used to get the actual PHP file that implements the view.
     *
     * @return \Concrete\Core\Filesystem\FileLocator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Get the path of the element view (relative to the webroot).
     *
     * @return string
     */
    public function getElementPath()
    {
        if ($controller = $this->getElementController()) {
            $element = $controller->getElement();
        } else {
            $element = $this->element;
        }

        return DIRNAME_ELEMENTS . '/' . $element . '.php';
    }

    /**
     * Check if the element view file exists.
     *
     * @return bool
     */
    public function exists()
    {
        $record = $this->locator->getRecord($this->getElementPath());

        return $record->exists();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Filesystem\LocatableFileInterface::getFileLocatorRecord()
     */
    public function getFileLocatorRecord()
    {
        return $this->locator->getRecord($this->getElementPath());
    }

    /**
     * Set a variable to be used in the view.
     *
     * @param string $key The name of the variable
     * @param mixed $value The value of the variable
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Get the element controller.
     *
     * @return \Concrete\Core\Controller\ElementController|null
     */
    public function getElementController()
    {
        if (!isset($this->controller)) {
            $path = DIRNAME_CONTROLLERS . '/element/' . $this->element
                . '.php';
            $class = 'Controller\\Element';
            $segments = explode('/', $this->element);
            foreach ($segments as $segment) {
                $class .= '\\' . camelcase($segment);
            }
            $class = overrideable_core_class($class, $path, $this->pkgHandle);
            if (class_exists($class)) {
                $this->controller = app($class, $this->controllerArguments);
                $this->controller->setPackageHandle($this->pkgHandle);
            }
        }

        return $this->controller;
    }

    /**
     * Render the element.
     */
    public function render()
    {
        $controller = $this->getElementController();
        $view = new FileLocatorView($this);
        $view->addScopeItems($this->variables);
        if ($controller) {
            $view->setController($controller);
        }
        $view->render();
    }

    /**
     * @return \Concrete\Core\Filesystem\FileLocator
     */
    protected function getBaseLocator()
    {
        $app = Facade::getFacadeApplication();
        $fs = new \Illuminate\Filesystem\Filesystem();
        $locator = new FileLocator($fs, $app);

        return $locator;
    }

    /**
     * @return \Concrete\Core\Filesystem\FileLocator
     */
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
}
