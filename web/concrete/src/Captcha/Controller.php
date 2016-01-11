<?php
namespace Concrete\Core\Captcha;

abstract class Controller
{
    /**
     * Note: feel free to make any of these blank.
     */

    /**
     * Shows an input for a particular captcha library.
     */
    abstract public function showInput();

    /**
     * Displays the graphical portion of the captcha.
     */
    abstract public function display();

    /**
     * Displays the label for this captcha library.
     */
    abstract public function label();
}
