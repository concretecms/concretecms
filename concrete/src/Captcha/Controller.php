<?php

namespace Concrete\Core\Captcha;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Support\Facade\Application;

/**
 * @deprecated Create a class that implements CaptchaInterface or a CaptchaWithPictureInterface interface
 */
abstract class Controller implements CaptchaWithPictureInterface
{
    /**
     * {@inheritdoc}
     */
    public function displayCaptchaPicture()
    {
        $app = Application::getFacadeApplication();
        $responseFactory = $app->make(ResponseFactoryInterface::class);

        return $responseFactory->notFound('Captcha without image');
    }
}
