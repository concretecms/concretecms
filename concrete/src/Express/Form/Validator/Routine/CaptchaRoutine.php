<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;
use Concrete\Core\Captcha\Service as CaptchaService;

class CaptchaRoutine implements RoutineInterface
{

    protected $captchaValidator;

    public function __construct(CaptchaService $service)
    {
        $this->captchaValidator = $service;
    }

    public function validate(ErrorList $error, Form $form, $requestType)
    {
        if (!$this->captchaValidator->check()) {
            $error->add(t('Incorrect captcha code.'));
            return false;
        }
        return true;
    }


}