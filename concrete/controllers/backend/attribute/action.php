<?php
namespace Concrete\Controller\Backend\Attribute;

use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Action extends AbstractController
{
    public function dispatch($action)
    {
        $arguments = explode('/', trim($action, '/'));
        if (count($arguments) > 2) { // there must at last be three arguments here
            if (isset($arguments[0])) {
                switch($arguments[0]) {
                    case 'key':
                        if (isset($arguments[1])) {
                            $key = Key::getByID($arguments[1]);
                            if (is_object($key)) {
                                $controller = $key->getController();
                            }
                        }
                        break;
                    case 'type':
                        if (isset($arguments[1])) {
                            $type = Type::getByID($arguments[1]);
                            if (is_object($type)) {
                                $controller = $type->getController();
                            }
                        }
                        break;
                }
            }
            if (isset($controller)) {
                $action = $arguments[2];
                $arguments = array_slice($arguments, 3);
                if (method_exists($controller, 'action_' . $action)) { //make sure the controller has the right method
                    $response = call_user_func_array(array($controller, 'action_' . $action), $arguments);
                    if ($response instanceof Response) {
                        return $response;
                    } else {
                        print $response;
                        $this->app->shutdown();
                    }
                }
            }
        }

        $response = new Response(t('Access Denied'));
        return $response;

    }


}
