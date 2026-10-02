<?php
namespace Concrete\Core\Express\Form\Control\Validator;

use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Error\ErrorList\ErrorList;
use Symfony\Component\HttpFoundation\Request;

class AttributeKeyControlValidator implements ValidatorInterface
{
    public function validateRequest(Control $control, Request $request)
    {
        if (!$control instanceof AttributeKeyControl) {
            $error = new ErrorList();
            $error->add(t('The control must be an instance of %s.', AttributeKeyControl::class));

            return $error;
        }
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
