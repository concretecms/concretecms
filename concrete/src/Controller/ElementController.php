<?php

namespace Concrete\Core\Controller;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\BasicFileView;
use Illuminate\Filesystem\Filesystem;

abstract class ElementController extends AbstractController
{
    /**
     * The handle of the package defining this element.
     *
     * @var string|null
     */
    protected $pkgHandle;

    /**
     * The view instance to be used when rendering the view.
     *
     * @var \Concrete\Core\View\BasicFileView|null
     */
    protected $view;

    /**
     * Get the element name.
     *
     * @return string
     */
    abstract public function getElement();

    /**
     * Get the view instance to be used when rendering the view.
     *
     * @return \Concrete\Core\View\BasicFileView
     *
     * @deprecated Consider using the Element class instead (see the description of the render() method for examples)
     * @see \Concrete\Core\Controller\ElementController::render()
     */
    public function getViewObject()
    {
        if ($this->view === null) {
            $locator = new FileLocator(new Filesystem(), Facade::getFacadeApplication());
            if ($this->pkgHandle) {
                $locator->addPackageLocation($this->pkgHandle);
            }
            $r = $locator->getRecord(DIRNAME_ELEMENTS . '/' . $this->getElement() . '.php');
            $this->view = new BasicFileView($r->getFile());
            $this->view->setController($this);
        }

        return $this->view;
    }

    /**
     * Get the handle of the package defining this element.
     *
     * @return string|null
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * Set the handle of the package defining this element.
     *
     * @param string|null $pkgHandle
     */
    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    /**
     * Render the element.
     *
     * @deprecated Consider using the Element class instead
     *
     * @example <code><pre>
     * // Initialize the Element (usually done in controllers or service classes)
     * $currentPage = $app->make(\Concrete\Core\Http\Request::class)->getCurrentPage();
     * $argumentsForElementControllerConstructor = [];
     *
     * /// ... with the element manager (extensible, better performance when the same element many times)
     * $elementManager = $app->make(\Concrete\Core\Filesystem\ElementManager::class);
     * $element = $elementManager->get('name', 'pkgHandle', $currentPage, $argumentsForElementControllerConstructor);
     *
     * /// ... without the element manager
     * $element = new \Concrete\Core\Filesystem\Element('name', 'pkgHandle', $currentPage, $argumentsForElementControllerConstructor);
     *
     * // Render the Element (usually done in views)
     * $element->render();
     * </pre></code>
     */
    public function render()
    {
        return $this->getViewObject()->render();
    }
}
