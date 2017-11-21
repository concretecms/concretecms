<?php
namespace Concrete\Core\Captcha;

/**
 * Interface that all captcha controllers must implement.
 */
interface CaptchaInterface
{
    /**
     * Print out the input where users should enter the captcha code.
     *
     * @param array $customInputAttributes Custom attributes for the input element
     */
    public function showInput(array $customInputAttributes = []);

    /**
     * Displays the graphical portion of the captcha.
     *
     * @param array $customAttributes Custom attributes for the image element
     * @param array $customImageAttributes
     */
    public function display(array $customImageAttributes = []);

    /**
     * Print out the label for this captcha library.
     *
     * @param string $inputID the ID of the captcha input
     */
    public function label($inputID = 'ccm-captcha-code');

    /**
     * Check if the user input is valid for the captcha.
     *
     * @param string $fieldName
     *
     * @return bool
     */
    public function check($fieldName = 'ccmCaptchaCode');
}
