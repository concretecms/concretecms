<?php
namespace Concrete\Controller\Backend;
use \Concrete\Core\Controller\Controller;
use \Concrete\Core\View\DialogView;
use Request;
use Loader;
abstract class UserInterface extends Controller {

    abstract protected function canAccess();
    protected $error;
    protected $validationToken;

    public function shouldRunControllerTask()
    {
        return $this->canAccess();
    }

    public function __construct()
    {
        $this->error = \Core::make('helper/validation/error');
        $this->view = new DialogView($this->viewPath);
        if (preg_match('/Concrete\\\Package\\\(.*)\\\Controller/i', get_class($this), $matches)) {
            $pkgHandle = uncamelcase($matches[1]);
            $this->view->setPackageHandle($pkgHandle);
        }
        $this->view->setController($this);
        $request = Request::getInstance();
        $this->request = $request;

        set_exception_handler(function($exception) {
            print $exception->getMessage();
        });
    }

    public function getViewObject()
    {
        if ($this->canAccess()) {
            return parent::getViewObject();
        }
        throw new \Exception(t('Access Denied'));
    }

    protected function validateAction()
    {
        $token = (isset($this->validationToken)) ? $this->validationToken : get_class($this);
        if (!Loader::helper('validation/token')->validate($token)) {
            $this->error->add(\Core::make('helper/validation/token')->getErrorMessage());
            return false;
        }
        if (!$this->canAccess()) {
            return false;
        }
        return true;
    }

    public function action()
    {
        $token = (isset($this->validationToken)) ? $this->validationToken : get_class($this);
        $url = call_user_func_array('parent::action', func_get_args());
        $url .= '?ccm_token=' . \Core::make('helper/validation/token')->generate($token);
        return $url;
    }

}