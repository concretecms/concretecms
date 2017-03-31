<?php
namespace Concrete\Core\Controller;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\BasicFileView;
use Illuminate\Filesystem\Filesystem;

abstract class ElementController extends AbstractController
{
    protected $pkgHandle;

    abstract public function getElement();

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
        /**
         * @var $locator FileLocator
         */
        $locator = new FileLocator(new Filesystem(), Facade::getFacadeApplication());
        if ($this->pkgHandle) {
            $locator->addPackageLocation($this->pkgHandle);
        }
        $r = $locator->getRecord(DIRNAME_ELEMENTS . '/' . $this->getElement() . '.php');
        $view = new BasicFileView($r->getFile());
        $view->setController($this);
        return $view->render();
    }

}
