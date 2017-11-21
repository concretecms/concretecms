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
     */
    public function showInput(array $customInputAttributes = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function display(array $customImageAttributes = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function label($inputID = 'ccm-captcha-code')
    {
    }

    /**
     * {@inheritdoc}
     */
    public function check($fieldName = 'ccmCaptchaCode')
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function displayCaptchaPicture()
    {
        return $this->responseFactory->notFound('Captcha without image');
    }
}
