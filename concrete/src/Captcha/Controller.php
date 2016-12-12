<?php

namespace Concrete\Core\Captcha;

abstract class Controller {

	/**
	 * Note: feel free to make any of these blank
	 */

	/**
	 * Shows an input for a particular captcha library
 	 */
	abstract function showInput();

	/**
	 * Displays the graphical portion of the captcha
	 */
	abstract function display();

	/**
	 * Displays the label for this captcha library
	 */
	abstract function label();

}
