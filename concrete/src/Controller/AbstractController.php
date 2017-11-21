<?php
namespace Concrete\Core\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\ResponseAssetGroup;
use Core;
use Request;
use View;

/**
 * Base class for all the controllers.
 */
abstract class AbstractController implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * The handles of the helpers to be returned by the getHelperObjects method.
     * These will be automatically sent to Views as variables.
     *
     * @var string[]
     */
    protected $helpers = [];

    /**
     * The values to be sent to views.
     *
     * @var array
     */
    protected $sets = [];

    /**
     * The action to be performed.
     *
     * @var string|null
     */
    protected $action;

    /**
     * The current request instance.
     *
     * @var Request|null
     */
    protected $request;

    /**
     * The action parameters.
     *
     * @var array|null
     */
    protected $parameters;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->request = Request::getInstance();
    }

    /**
     * Get the current request instance.
     *
     * @return Request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = Request::getInstance();
        }

        return $this->request;
    }

    /**
     * Set the current request instance.
     *
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Add an asset required in views.
     * This function accept the same parameters as the requireAsset method of the ResponseAssetGroup.
     *
     * @see ResponseAssetGroup::requireAsset
     */
    public function requireAsset()
    {
        $args = func_get_args();
        $r = ResponseAssetGroup::get();
        call_user_func_array([$r, 'requireAsset'], $args);
    }

    /**
     * Adds an item to the view's header. This item will then be automatically printed out before the <body> section of the page.
     *
     * @param string $item
     */
    public function addHeaderItem($item)
    {
        $v = View::getInstance();
        $v->addHeaderItem($item);
    }

    /**
     * Adds an item to the view's footer. This item will then be automatically printed out before the </body> section of the page.
     *
     * @param string $item
     */
    public function addFooterItem($item)
    {
        $v = View::getInstance();
        $v->addFooterItem($item);
    }

    /**
     * Set a value to be sent to the view.
     *
     * @param string $key The name of the value
     * @param mixed $val The value
     */
    public function set($key, $val)
    {
        $this->sets[$key] = $val;
    }

    /**
     * Get the values to be sent to views.
     *
     * @return array
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * Should the action be executed? Override this method to answer something different than true.
     *
     * @return bool
     */
    public function shouldRunControllerTask()
    {
        return true;
    }

    /**
     * Get the the helpers that will be be automatically sent to Views as variables.
     * Array keys are the variable names, array values are the helper instances.
     *
     * @return array
     */
    public function getHelperObjects()
    {
        $helpers = [];
        foreach ($this->helpers as $handle) {
            $h = Core::make('helper/' . $handle);
            $helpers[(str_replace('/', '_', $handle))] = $h;
        }

        return $helpers;
    }

    /**
     * Get the whole $_GET array or a specific querystring value.
     *
     * @param string|null $key set to null to get the whole $_GET array, or a string to get a specific value in the controller sets or from the querystring parameters
     * @param mixed $defaultValue what to return if $key is specified but it does not exist neither in the sets nor in the querystring
     *
     * @return mixed
     */
    public function get($key = null, $defaultValue = null)
    {
        if ($key == null) {
            return $_GET;
        }

        if (isset($this->sets[$key])) {
            return $this->sets[$key];
        }

        $val = $this->getRequest()->get($key, $defaultValue);

        return $val;
    }

    /**
     * @deprecated use the getAction() method
     */
    public function getTask()
    {
        return $this->getAction();
    }

    /**
     * Get the action to be performed.
     *
     * @return string|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the action parameters.
     *
     * @return array|null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Override this method to perform controller initializations.
     */
    public function on_start()
    {
    }

    /**
     * Override this method to do something right before the view is rendered.
     * For instance, you can call $this->set('variableName', $variableValue) to send the view additional sets.
     */
    public function on_before_render()
    {
    }

    /**
     * @deprecated Use $this->getRequest()->isPost();
     */
    public function isPost()
    {
        return Request::isPost();
    }

    /**
     * Get the whole $_POST array or a specific posted value.
     *
     * @param string|null $key set to null to get the whole $_POST array, or a string to get a specific posted value (resulting strings will be trimmed)
     * @param mixed $defaultValue what to return if $key is specified but it does not exist in the $_POST
     *
     * @return mixed
     */
    public function post($key = null, $defaultValue = null)
    {
        return Request::post($key, $defaultValue);
    }

    /**
     * Redirect the clients to a specific URL/page (specify path(s) as argument(s) of this function).
     *
     * @deprecated you should return a Response instance from your methods
     */
    public function redirect()
    {
        $args = func_get_args();
        $r = call_user_func_array(['Redirect', 'to'], $args);
        $r->send();
        exit;
    }

    /**
     * @deprecated use the runAction method
     *
     * @param mixed $action
     * @param mixed $parameters
     */
    public function runTask($action, $parameters)
    {
        $this->runAction($action, $parameters);
    }

    /**
     * Perform an action of this controller (if shouldRunControllerTask returns true).
     *
     * @param string $action the action to be performed
     * @param array $parameters the action parameters
     *
     * @return mixed in case the action is executed, you'll receive the result of the action, or NULL otherwise
     */
    public function runAction($action, $parameters = [])
    {
        $this->action = $action;
        $this->parameters = $parameters;
        if (is_callable([$this, $action])) {
            if ($this->shouldRunControllerTask()) {
                return call_user_func_array([$this, $action], $parameters);
            }
        }
    }

    /**
     * Get the whole $_REQUEST array or a specific requested value.
     *
     * @param string|null $key set to null to get the whole $_REQUEST array, or a string to get a specific value in $_GET or in $_POST
     *
     * @return mixed
     */
    public function request($key = null)
    {
        return Request::request($key);
    }
}
