<?php

namespace Concrete\Core\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Http\ResponseAssetGroup;
use Core;
use Request;
use View;

abstract class AbstractController implements ApplicationAwareInterface
{

    protected $helpers = array();
    protected $sets = array();
    protected $action;
    protected $request;
    protected $parameters;

    /** @var Application */
    protected $app;

    public function __construct()
    {
        $this->request = Request::getInstance();
    }

    public function requireAsset()
    {
        $args = func_get_args();
        $r = ResponseAssetGroup::get();
        call_user_func_array(array($r, 'requireAsset'), $args);
    }

    /**
     * Adds an item to the view's header. This item will then be automatically printed out before the <body> section of the page
     *
     * @param string $item
     * @return void
     */

    public function addHeaderItem($item)
    {
        $v = View::getInstance();
        $v->addHeaderItem($item);
    }

    /**
     * Adds an item to the view's footer. This item will then be automatically printed out before the </body> section of the page
     *
     * @param string $item
     * @return void
     */
    public function addFooterItem($item)
    {
        $v = View::getInstance();
        $v->addFooterItem($item);
    }

    public function set($key, $val)
    {
        $this->sets[$key] = $val;
    }

    public function getSets()
    {
        return $this->sets;
    }

    public function shouldRunControllerTask()
    {
        // this assumes the task has already been checked for validity.
        return true;
    }

    public function getHelperObjects()
    {
        $helpers = array();
        foreach ($this->helpers as $handle) {
            $h = Core::make('helper/' . $handle);
            $helpers[(str_replace('/', '_', $handle))] = $h;
        }
        return $helpers;
    }

    public function get($key = null, $defaultValue = null)
    {
        if ($key == null) {
            return $_GET;
        }

        if (isset($this->sets[$key])) {
            return $this->sets[$key];
        }

        if (!isset($this->request)) {
            $this->request = Request::createFromGlobals();
        }

        $val = $this->request->get($key, $defaultValue);
        return $val;

    }

    public function getTask()
    {
        return $this->getAction();
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function on_start()
    {
    }

    public function on_before_render()
    {
    }

    /**
     * @deprecated
     */
    public function isPost()
    {
        return Request::isPost();
    }

    public function post($key = null, $defaultValue = null)
    {
        return Request::post($key, $defaultValue);
    }

    public function redirect()
    {
        $args = func_get_args();
        $r = call_user_func_array(array('Redirect', 'to'), $args);
        $r->send();
        exit;
    }

    public function runTask($action, $parameters)
    {
        $this->runAction($action, $parameters);
    }

    public function runAction($action, $parameters = array())
    {
        $this->action = $action;
        $this->parameters = $parameters;
        if (is_callable(array($this, $action))) {
            if ($this->shouldRunControllerTask()) {
                return call_user_func_array(array($this, $action), $parameters);
            }
        }
    }

    public function request($key = null)
    {
        return Request::request($key);
    }

    /**
     * Set the application object
     *
     * @param \Concrete\Core\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->app = $application;
    }

}
