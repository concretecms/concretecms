<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\DialogView;
use Request;

abstract class UserInterface extends Controller
{
    /**
     * The current errors container.
     *
     * @var \Concrete\Core\Error\ErrorList\ErrorList
     */
    protected $error;

    /**
     * An identifier to be used when checking tokens.
     *
     * @var string|null
     */
    protected $validationToken;

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
        $this->error = $this->app->make('error');
        $this->view = new DialogView($this->viewPath);
        if (preg_match('/Concrete\\\Package\\\(.*)\\\Controller/i', get_class($this), $matches)) {
            $pkgHandle = uncamelcase($matches[1]);
            $this->view->setPackageHandle($pkgHandle);
        }
        $this->view->setController($this);
        $this->request = Request::getInstance();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::shouldRunControllerTask()
     */
    public function shouldRunControllerTask()
    {
        return $this->canAccess();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::getViewObject()
     */
    public function getViewObject()
    {
        if ($this->canAccess()) {
            return parent::getViewObject();
        }
        throw new UserMessageException(t('Access Denied'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::action()
     */
    public function action()
    {
        $token = isset($this->validationToken) ? $this->validationToken : get_class($this);
        $url = (string) call_user_func_array('parent::action', func_get_args());
        $url .= (strpos($url, '?') === false ? '?' : '&') . $this->app->make('token')->getParameter($token);

        return $url;
    }

    /**
     * Can the current page be accessed?
     *
     * @return bool
     */
    abstract protected function canAccess();

    /**
     * Check whether the token is valid and if the current page be accessed.
     *
     * @return bool
     */
    protected function validateAction()
    {
        $token = (isset($this->validationToken)) ? $this->validationToken : get_class($this);
        if (!$this->app->make('token')->validate($token)) {
            $this->error->add($this->app->make('token')->getErrorMessage());

            return false;
        }
        if (!$this->canAccess()) {
            return false;
        }

        return true;
    }
}
