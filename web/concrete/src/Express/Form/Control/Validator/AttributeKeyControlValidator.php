<?php
namespace Concrete\Core\Express\Form\Control\Validator;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Error\Error;
use Symfony\Component\HttpFoundation\Request;

class AttributeKeyControlValidator implements ValidatorInterface
{
    public function validateRequest(Control $control, Request $request)
    {
        $key = $control->getAttributeKey();
        $controller = $key->getController();
        $response = $controller->validateForm($controller->post());
        if ($response === false) {
            $e = \Core::make('error');
            $e->add(t('The field %s is required', $control->getControlLabel()));

            return $e;
        } elseif ($response instanceof \Concrete\Core\Error\Error) {
            return $response;
        }
    }
}
