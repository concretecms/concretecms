<?php

namespace Concrete\Core\Captcha;

/**
 * Interface that allows captcha controllers to controll their own error messages
 */
interface CaptchaWithErrorInterface extends CaptchaInterface
{
	 /**
     * Check if the user input is valid for the captcha.
     * Accepts an error list by reference.
     *
     * @param ErrorList $error
     * @return bool
     */
    public function checkWithErrorList(ErrorList &$error);
}
