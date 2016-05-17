<?php
namespace Concrete\Core\Express\Form\Control\Validator;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class AttributeKeyControlValidator implements ValidatorInterface
{
    public function validateRequest(Control $control, Request $request)
    {
        $key = $control->getAttributeKey();
        $controller = $key->getController();
        $validator = $controller->getValidator();
        $response = $validator->validateSaveValueRequest($controller, $request, $control->isRequired());
        if (!$response->isValid()) {
            $error = $response->getErrorObject();
            return $error;
        }
        return true;
    }
}
