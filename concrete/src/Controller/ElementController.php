<?php
namespace Concrete\Core\Controller;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\BasicFileView;
use Illuminate\Filesystem\Filesystem;

abstract class ElementController extends AbstractController
{
    protected $pkgHandle;
    protected $view;

    abstract public function getElement();

    public function getViewObject()
    {
        if (!isset($this->view)) {
            /**
             * @var $locator FileLocator
             */
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
     * @return mixed
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * @param mixed $pkgHandle
     */
    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    /**
     * @deprecated
     * Consider using the Element class instead.
     */
    public function render()
    {
        return $this->getViewObject()->render();
    }

}
