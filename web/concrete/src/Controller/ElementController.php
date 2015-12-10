<?php

namespace Concrete\Core\Controller;

use Concrete\Core\Config\Renderer;
use Concrete\Core\View\ElementView;
use Illuminate\Config\Repository;
use Request;
use PageTheme;
use View;
use Route;

abstract class ElementController extends AbstractController
{

    protected $pkgHandle;
    protected $view;

    abstract public function getElement();

    public function getViewObject()
    {
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


    public function __construct()
    {
        $this->view = new ElementView($this->getElement(), $this->getPackageHandle());
        $this->view->setController($this);
    }


    public function render()
    {
        return $this->getViewObject()->render();
    }

}
