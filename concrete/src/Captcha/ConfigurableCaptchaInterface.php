<?php

namespace Concrete\Core\Captcha;

/**
 * Interface that configurable captcha controllers can implement to let users customize the captcha.
 */
interface ConfigurableCaptchaInterface
{
    /**
     * Set the custom attributes for the captcha label field.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setLabelAttributes(array $attributes);

    /**
     * Get the custom attributes for the captcha label field.
     *
     * @return array
     */
    public function getLabelAttributes();

    /**
     * Set the custom attributes for the captcha image.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setPictureAttributes(array $attributes);

    /**
     * Get the custom attributes for the captcha picture.
     *
     * @return array
     */
    public function getPictureAttributes();

    /**
     * Set the custom attributes of the captcha input field.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setInputAttributes(array $attributes);

    /**
     * Get the custom attributes of the captcha input field.
     *
     * @return array
     */
    public function getInputAttributes();
}
