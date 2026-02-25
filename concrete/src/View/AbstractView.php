<?php
namespace Concrete\Core\View;

use Concrete\Core\Http\ResponseAssetGroup;
use Request;
use URL;
use Core;

/**
 * Abstract view class
 */
abstract class AbstractView
{
    protected static $requestInstance;
    protected static $requestInstances = array();
    protected $scopeItems = array();
    public $controller;
    protected $template;
    protected $outputAssets = array();

    public function getViewTemplate()
    {
        return $this->template;
    }

    public function addScopeItems($items)
    {
        foreach ($items as $key => $value) {
            $this->scopeItems[$key] = $value;
        }
    }

    public static function getRequestInstance()
    {
        if (!self::$requestInstance instanceof View) {
            View::setRequestInstance(new View());
        }

        return self::$requestInstance;
    }

    /**
     * @param mixed $mixed object to view
     */

    public function __construct($mixed = false)
    {
        $this->constructView($mixed);
    }

    protected static function setRequestInstance(View $v)
    {
        View::$requestInstances[] = $v;
        self::$requestInstance = $v;
    }

    protected static function revertRequestInstance()
    {
        array_pop(View::$requestInstances);
        self::$requestInstance = last(View::$requestInstances);

        if (self::$requestInstance === false) {
            // 'last' can return false if there are no request instances.
            // In that case a fresh instance should be retrieved.
            self::$requestInstance = View::getInstance();
        }
    }

    abstract public function start($state);

    public function startRender()
    {
        if (is_object($this->controller)) {
            $this->controller->on_before_render();
        }
    }
    abstract protected function constructView($mixed);

    abstract public function setupRender();

    abstract public function finishRender($contents);

    abstract public function action($action);

    public function addHeaderAsset($asset)
    {
        $r = ResponseAssetGroup::get();
        $r->addHeaderAsset($asset);
    }

    public function addFooterAsset($asset)
    {
        $r = ResponseAssetGroup::get();
        $r->addFooterAsset($asset);
    }

    public function addOutputAsset($asset)
    {
        $r = ResponseAssetGroup::get();
        $r->addOutputAsset($asset);
    }

    public function requireAsset($assetType, $assetHandle = false)
    {
        $r = ResponseAssetGroup::get();
        $r->requireAsset($assetType, $assetHandle);
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function setViewTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Returns the value of the item in the POST array.
     *
     * @param $key
     */
    public function post($key, $defaultValue = null)
    {
        $r = Request::getInstance();

        return $r->post($key, $defaultValue);
    }

    abstract protected function onBeforeGetContents();

    protected function postProcessViewContents($contents)
    {
        return $contents;
    }
    protected function onAfterGetContents()
    {
    }

    public function getScopeItems()
    {
        if (is_object($this->controller)) {
            $sets = $this->controller->getSets();
            $helpers = $this->controller->getHelperObjects();
            $return = array_merge($this->scopeItems, $sets, $helpers);
        } else {
            $return = $this->scopeItems;
        }
        $return['view'] = $this;
        $return['controller'] = $this->controller;
        return $return;
    }

    public function render($state = false)
    {
        if ($this instanceof View) {
            $this->setRequestInstance($this);
        }
        $this->start($state);
        $this->setupRender();
        $this->startRender();
        $scopeItems = $this->getScopeItems();
        $contents = $this->renderViewContents($scopeItems);
        $contents = $this->postProcessViewContents($contents);
        $response = $this->finishRender($contents);
        if ($this instanceof View) {
            $this->revertRequestInstance();
        }

        return $response;
    }

    public function renderViewContents($scopeItems)
    {
        if (file_exists($this->template)) {
            extract($scopeItems);
            ob_start();
            $this->onBeforeGetContents();
            include $this->template;
            $this->onAfterGetContents();
            $contents = ob_get_contents();
            ob_end_clean();

            return $contents;
        }
    }

    /**
     * URL is a utility function that is used inside a view to setup urls w/tasks and parameters.
     *
     * @param string $action
     * @param string $task
     *
     * @return string $url
     */
    public static function url($action, $task = null)
    {
        $args = func_get_args();

        return (string) call_user_func_array(array('URL', 'to'), $args);
    }

    public function renderError($title, $error, $errorObj = null)
    {
        Core::make('helper/concrete/ui')->renderError($title, $error);
    }

    /**
     */
    public function addHeaderItem($item)
    {
        $this->addHeaderAsset($item);
    }

    /**
     */
    public function addFooterItem($item)
    {
        $this->addFooterAsset($item);
    }

    /**
     * Get an instance of the View.
     *
     * Note: In versions before 8.5.0a3, this method may
     * return 'false' if it's called after the page
     * is rendered (for example in middleware).
     *
     * @return View
     */
    public static function getInstance()
    {
        return View::getRequestInstance();
    }
}
