<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Captcha\CaptchaWithPictureInterface;
use Concrete\Core\Captcha\Library as CaptchaLibrary;
use Concrete\Core\Captcha\NoCaptchaController;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $app = $this->app;
        $singletons = [
            'helper/validation/antispam' => '\Concrete\Core\Antispam\Service',
            'helper/validation/file' => '\Concrete\Core\File\ValidationService',
            'helper/validation/form' => '\Concrete\Core\Form\Service\Validation',
            'helper/validation/identifier' => '\Concrete\Core\Utility\Service\Identifier',
            'helper/validation/ip' => '\Concrete\Core\Permission\IPService',
            'helper/validation/numbers' => '\Concrete\Core\Utility\Service\Validation\Numbers',
            'helper/validation/strings' => '\Concrete\Core\Utility\Service\Validation\Strings',
            'helper/validation/banned_words' => '\Concrete\Core\Validation\BannedWord\Service',
            'helper/security' => '\Concrete\Core\Validation\SanitizeService',
            'ip' => '\Concrete\Core\Permission\IPService',
        ];
        $registers = [
            'helper/validation/token' => '\Concrete\Core\Validation\CSRF\Token',
            'helper/validation/error' => '\Concrete\Core\Error\ErrorList\ErrorList',
            'token' => '\Concrete\Core\Validation\CSRF\Token',
        ];

        foreach ($singletons as $key => $value) {
            $app->singleton($key, $value);
        }
        foreach ($registers as $key => $value) {
            $app->bind($key, $value);
        }
        $app->bind(CaptchaInterface::class, function () use ($app) {
            $library = CaptchaLibrary::getActive();
            $controller = $library === null ? null : $library->getController();
            if ($controller === null) {
                $controller = $app->make(NoCaptchaController::class);
            }

            return $controller;
        });
        $app->alias(CaptchaInterface::class, 'helper/validation/captcha');
        $app->alias(CaptchaInterface::class, 'captcha');
        $app->bind(CaptchaWithPictureInterface::class, function () use ($app) {
            $controller = $app->make(CaptchaInterface::class);
            if (!$controller instanceof CaptchaWithPictureInterface) {
                // Grant backward compatibility for custom libraries not explicitly implementing the CaptchaWithPictureInterface class
                if (!method_exists($controller, 'displayCaptchaPicture')) {
                    $controller = $app->make(NoCaptchaController::class);
                }
            }

            return $controller;
        });
    }
}
