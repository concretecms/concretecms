<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorBag\ErrorBag;
use Symfony\Component\HttpFoundation\Request;

class Validator
{
    protected $request;
    protected $error;

    public function __construct(ErrorBag $error, Request $request)
    {
        $this->request = $request;
        $this->error = $error;
    }

    public function validate(Form $form)
    {
        $token = \Core::make('token');
        if (!$token->validate('express_form', $this->request->request->get('ccm_token'))) {
            $this->error->add($token->getErrorMessage());
        }
        foreach ($form->getControls() as $control) {
            $type = $control->getControlType();
            $validator = $type->getValidator($control);
            if (is_object($validator)) {
                $e = $validator->validateRequest($control, $this->request);
                if (is_object($e) && $e->has()) {
                    $this->error->add($e);
                }
            }
        }

        return $this->error;
    }
}
