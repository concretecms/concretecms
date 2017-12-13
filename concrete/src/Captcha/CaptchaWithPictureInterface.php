<?php

namespace Concrete\Core\Captcha;

/**
 * Interface that captcha controllers .
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
