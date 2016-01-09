<?php

namespace Concrete\Core\Controller;
use Concrete\Core\Config\Renderer;
use Illuminate\Config\Repository;
use Request;
use PageTheme;
use View;
use Route;

class Controller extends AbstractController {

	protected $view;
	protected $viewPath;
	protected $theme;
	protected $controllerActionPath;
    protected $themeViewTemplate;

	public function setViewObject(\Concrete\Core\View\AbstractView $view) {
		$this->view = $view;
	}

	public function setTheme($mixed) {
		if ($mixed instanceof PageTheme) {
			$this->theme = $mixed->getThemeHandle();
		} else {
			$this->theme = $mixed;
		}
	}

	public function getTheme() {
		if (is_object($this->view)) {
			$tmpTheme = Route::getThemeByRoute($this->view->getViewPath());
			if ($tmpTheme) {
				return $tmpTheme[0];
			}
		}

        if (isset($this->theme)) {
            return $this->theme;
        }
	}

    public function setThemeViewTemplate($template)
    {
        $this->themeViewTemplate = $template;
    }
    public function getThemeViewTemplate()
    {
		if (isset($this->themeViewTemplate)) {
			return $this->themeViewTemplate;
		}

		if (is_object($this->view)) {
			$tmpTheme = Route::getThemeByRoute($this->view->getViewPath());
			if ($tmpTheme && isset($tmpTheme[1])) {
				return $tmpTheme[1];
			}
		}

		return FILENAME_THEMES_VIEW;
    }

	public function getControllerActionPath() {
		if (isset($this->controllerActionPath)) {
			return $this->controllerActionPath;
		}

		$request = Request::getInstance();
		return $request->getPathInfo();
	}

	public function __construct() {
        parent::__construct();
		if ($this->viewPath) {
			$this->view = new View($this->viewPath);
			if (preg_match('/Concrete\\\Package\\\(.*)\\\Controller/i', get_class($this), $matches)) {
                $pkgHandle = uncamelcase($matches[1]);
                $this->view->setPackageHandle($pkgHandle);
			}
			$this->view->setController($this);
		}
	}

	public function getViewObject() {
		if ($this->view) {
			$this->view->setController($this);
			$this->view->setViewTheme($this->getTheme());
			return $this->view;
		}
	}

	public function action() {
		$a = func_get_args();
		array_unshift($a, $this->getControllerActionPath());
		$ret = call_user_func_array(array($this->view, 'url'), $a);
		return $ret;
	}

}
