<?php
namespace Concrete\Core\Controller;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Theme\Theme as PageTheme;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\View\View;

class Controller extends AbstractController
{
    protected $view;
    protected $viewPath;
    protected $theme;
    protected $controllerActionPath;
    protected $themeViewTemplate;

    public function setViewObject(View $view)
    {
        $this->view = $view;
    }

    public function setTheme($mixed)
    {
        if ($mixed instanceof PageTheme) {
            $this->theme = $mixed->getThemeHandle();
        } else {
            $this->theme = $mixed;
        }
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setThemeViewTemplate($template)
    {
        $this->themeViewTemplate = $template;
    }

    /**
     * Returns the wrapper file that holds the content of the view. Usually view.php
     * @return string
     */
    public function getThemeViewTemplate()
    {
        if (isset($this->view)) {
            $templateFromView = $this->view->getViewTemplateFile();
        }

        if (isset($this->themeViewTemplate) && $templateFromView == FILENAME_THEMES_VIEW) {
            return $this->themeViewTemplate;
        }

        if (!isset($templateFromView)) {
            $templateFromView = FILENAME_THEMES_VIEW;
        }

        return $templateFromView;
    }

    public function getControllerActionPath()
    {
        if (isset($this->controllerActionPath)) {
            return $this->controllerActionPath;
        }

        $request = Request::getInstance();

        return $request->getPathInfo();
    }

    public function __construct()
    {
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

    public function flash($key, $value, $isHTML = false)
    {
        $session = Facade::getFacadeApplication()->make('session');
        $session->getFlashBag()->add('page_message', array($key, $value, $isHTML));
    }

    public function getViewObject()
    {
        if ($this->view) {
            $this->view->setController($this);
            $this->view->setViewTheme($this->getTheme());

            return $this->view;
        }
    }

    public function action()
    {
        $a = func_get_args();
        array_unshift($a, $this->getControllerActionPath());
        $ret = call_user_func_array(array($this->view, 'url'), $a);

        return $ret;
    }
}
