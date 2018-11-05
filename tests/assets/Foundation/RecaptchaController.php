<?php

namespace Application\Concrete\Captcha;

use Concrete\Core\Captcha\CaptchaInterface;

class RecaptchaController implements CaptchaInterface
{
    public function display(array $customImageAttributes = [])
    {
    }

    public function showInput(array $customAttributes = [])
    {
        // TODO: Implement showInput() method.
    }

    public function label($inputID = 'ccm-captcha-code')
    {
    }

    public function check($fieldName = 'ccmCaptchaCode')
    {
    }
}
