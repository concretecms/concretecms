<?php

namespace Concrete\Core\Captcha;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\ResponseFactoryInterface;

/**
 * Captcha controller used when there's no active captcha library.
 */
class NoCaptchaController extends AbstractController implements CaptchaWithPictureInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * Initialize the instance.
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        parent::__construct();
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::label()
     */
    public function label()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::display()
     */
    public function display()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::showInput()
     */
    public function showInput()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::check()
     */
    public function check()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaWithPictureInterface::displayCaptchaPicture()
     */
    public function displayCaptchaPicture()
    {
        return $this->responseFactory->notFound('Captcha without image');
    }
}
