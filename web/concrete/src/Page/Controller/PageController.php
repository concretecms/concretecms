<?php

namespace Concrete\Core\Page\Controller;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Foundation\Environment;
use Concrete\Core\Routing\Redirect;
use Page;
use Request;
use Controller;
use Core;
use Concrete\Core\Page\View\PageView;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    protected $supportsPageCache = false;
    protected $action;
    protected $passThruBlocks = array();
    protected $parameters = array();

    public function supportsPageCache()
    {
        return $this->supportsPageCache;
    }

    public function __construct(\Concrete\Core\Page\Page $c)
    {
        parent::__construct();
        $this->c = $c;
        $this->view = new PageView($this->c);
        $this->set('html', Core::make('\Concrete\Core\Html\Service\Html'));
    }

    /**
     * Given either a path or a Page object, this is a shortcut to
     * 1. Grab the controller of THAT page.
     * 2. Grab the view of THAT controller
     * 3. Render that view.
     * 4. Exit – so we immediately stop all other output in the controller that
     * called render().
     *
     * @param @string|\Concrete\Core\Page\Page $var
     */
    public function replace($var)
    {
        if (!($var instanceof \Concrete\Core\Page\Page)) {
            $var = \Page::getByPath($var);
        }

        $request = \Request::getInstance();
        $request->setCurrentPage($var);
        $controller = $var->getPageController();
        $controller->on_start();
        $controller->runAction('view');
        $controller->on_before_render();
        $view = $controller->getViewObject();
        print $view->render();
        exit;
    }

    public function getSets()
    {
        $sets = parent::getSets();
        $session = Core::make('session');
        if ($session->getFlashBag()->has('page_message')) {
            $value = $session->getFlashBag()->get('page_message');
            foreach($value as $message) {
                $sets[$message[0]] = $message[1];
            }
        }
        return $sets;
    }



    /**
     * Given a path to a single page, this command uses the CURRENT controller and renders
     * the contents of the single page within this request. The current controller is not
     * replaced, and has already fired (since it is meant to be called from within a view() or
     * similar method).
     *
     * @param @string
     */
    public function render($path, $pkgHandle = null)
    {
        $view = $this->getViewObject();

        $env = Environment::get();
        $path = trim($path, '/');
        $a = $path . '/' . FILENAME_COLLECTION_VIEW;
        $b = $path . '.php';

        $r = $env->getRecord(DIRNAME_PAGES . '/' . $a);
        if ($pkgHandle) {
            $view->setPackageHandle($pkgHandle);
        }
        if ($r->exists()) {
            $view->renderSinglePageByFilename($a);
        } else {
            $view->renderSinglePageByFilename($b);
        }
    }

    public function getPageObject()
    {
        return $this->c;
    }

    public function flash($key, $value)
    {
        $session = Core::make('session');
        $session->getFlashBag()->add('page_message', array($key, $value));
    }

    public function getTheme()
    {
        if ($this->theme === null) {
            $theme = parent::getTheme();
            if (!$theme) {
                $theme = $this->c->getCollectionThemeObject();
                if (is_object($theme)) {
                    $this->theme = $theme->getThemeHandle();
                }
            } else {
                $this->theme = $theme;
            }
        }

        return $this->theme;
    }

    public function getRequestAction()
    {
        return $this->action;
    }

    public function getRequestActionParameters()
    {
        return $this->parameters;
    }

    public function getControllerActionPath()
    {
        if (isset($this->controllerActionPath)) {
            return $this->controllerActionPath;
        }

        if (is_object($this->view)) {
            return $this->view->getViewPath();
        }
    }

    public function setupRequestActionAndParameters(Request $request)
    {
        $task = substr($request->getPath(), strlen($this->c->getCollectionPath()) + 1);
        $task = str_replace('-/', '', $task);
        $taskparts = explode('/', $task);
        if (isset($taskparts[0]) && $taskparts[0] !== '') {
            $method = $taskparts[0];
        } elseif (is_object($this->c) && is_callable(array($this, $this->c->getCollectionHandle()))) {
            $method = $this->c->getCollectionHandle();
        } else {
            $method = 'view';
        }

        $foundTask = false;
        try {
            $r = new \ReflectionMethod(get_class($this), $method);
            $cl = $r->getDeclaringClass();
            if (is_object($cl)) {
                if ($cl->getName() != 'Concrete\Core\Controller\Controller' && strpos($method, 'on_') !== 0 && strpos($method, '__') !== 0 && $r->isPublic()) {
                    $foundTask = true;
                }
            }
        } catch (\Exception $e) {
        }

        if ($foundTask) {
            $this->action = $method;
            if (isset($taskparts[1])) {
                array_shift($taskparts);
                $this->parameters = $taskparts;
            }
        } else {
            $this->action = 'view';
            if ($taskparts[0] !== '') {
                $this->parameters = $taskparts;
            }
        }
    }

    public function isValidControllerTask($action, $parameters = array())
    {
        $valid = true;
        if (!is_callable(array($this, $this->action)) && count($this->parameters) > 0) {
            $valid = false;
        }

        if (is_callable(array($this, $this->action))  && (get_class($this) != '\Concrete\Controller\PageForbidden')) {
            // we use reflection to see if the task itself, which now much exist, takes fewer arguments than
            // what is specified
            $r = new \ReflectionMethod(get_class($this), $this->action);
            if ($r->getNumberOfParameters() < count($this->parameters)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @access private
     *
     * @param Block $b
     * @param BlockController $controller
     */
    public function setPassThruBlockController(Block $b, BlockController $controller)
    {
        $this->passThruBlocks[$b->getBlockID()] = $controller;
    }

    public function getPassThruBlockController(Block $b)
    {
        $bID = $b->getBlockID();

        return isset($this->passThruBlocks[$bID]) ? $this->passThruBlocks[$bID] : null;
    }

    public function validateRequest()
    {
        $valid = true;

        if (!$this->isValidControllerTask($this->action, $this->parameters)) {
            $valid = false;
            // we check the blocks on the page.
            $blocks = array_merge($this->getPageObject()->getBlocks(), $this->getPageObject()->getGlobalBlocks());

            foreach ($blocks as $b) {
                $controller = $b->getController();
                list($method, $parameters) = $controller->getPassThruActionAndParameters($this->parameters);
                if ($controller->isValidControllerTask($method, $parameters)) {
                    $controller->on_start();
                    $response = $controller->runAction($method, $parameters);
                    if ($response instanceof Response) {
                        return $response;
                    }
                    // old school blocks have already terminated at this point. They are redirecting
                    // or exiting. But new blocks like topics, etc... can actually rely on their $set
                    // data persisting and being passed into the view.

                    // so if we make it down here we have to return true –so that we don't fire a 404.
                    $valid = true;

                    // then, we need to save the persisted data that may have been set.
                    $controller->setPassThruBlockController($this);
                }
            }

            if (!$valid) {
                // finally, we check additional page paths.
                $paths = $this->getPageObject()->getAdditionalPagePaths();
                foreach ($paths as $path) {
                    if ($path->getPagePath() == $this->request->getPath()) {
                        // This is an additional page path to a page. We 301 redirect.
                        return Redirect::page($this->getPageObject(), 301);
                    }
                }
            }
        }

        return $valid;
    }
}
