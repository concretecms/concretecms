<?php
namespace Concrete\Core\Captcha;

class Service
{
    public function __call($nm, $args)
    {
        if (!isset($this->controller)) {
            $captcha = \Concrete\Core\Captcha\Library::getActive();
            $this->controller = $captcha->getController();
        }

        if (method_exists($this->controller, $nm)) {
            return call_user_func_array(array($this->controller, $nm), $args);
        }
    }
}
