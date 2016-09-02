<?php
namespace Concrete\Controller\SinglePage;

use Concrete\Core\Http\Response;
use Concrete\Core\Page\Controller\PageController;

class PageNotFound extends PageController
{

    public function validateRequest()
    {
        return true;
    }

    public function view()
    {
        $view = $this->getViewObject();
        $contents = $view->render();

        return new Response($contents, 404);
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }

        return $this->view();
    }

}
