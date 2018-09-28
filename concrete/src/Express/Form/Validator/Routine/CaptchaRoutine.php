<?php

namespace Concrete\Core\Express\Form\Validator\Routine;

use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;

class CaptchaRoutine implements RoutineInterface
{
    /**
     * @var CaptchaInterface
     */
    protected $captchaValidator;

    public function __construct(CaptchaInterface $service)
    {
        $this->captchaValidator = $service;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(ErrorList $error, Form $form, $requestType)
    {
        if (!$this->captchaValidator->check()) {
            $error->add(t('Incorrect captcha code.'));

            return false;
        }

        return true;
    }
}
