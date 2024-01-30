<?php

namespace Concrete\Core\Captcha;

/**
 * Interface that all captcha controllers must implement.
 */
interface CaptchaInterface
{
    /**
     * Print out the label for this captcha library.
     */
    public function label();

    /**
     * Displays the graphical portion of the captcha.
     */
    public function display();

    /**
     * Print out the input where users should enter the captcha code.
     */
    public function showInput();

    /**
     * Check if the user input is valid for the captcha.
     *
     * @return bool
     */
    public function check();
	
	 /**
     * Check if the user input is valid for the captcha.
     * Accepts an error list by reference.
     *
     * @param ErrorList $error
     * @return bool
     */
    public function checkWithErrorList(ErrorList &$error);
}
