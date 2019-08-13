<?php

namespace Concrete\Core\Captcha;

/**
 * Interface that captcha controllers .
 * @since 8.3.0
 */
interface CaptchaWithPictureInterface extends CaptchaInterface
{
    /**
     * Sends the picture data to the client.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayCaptchaPicture();
}
