<?php

namespace Concrete\Core\Captcha;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Form\Service\Form as FormService;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use HtmlObject\Image;
use HtmlObject\Input;
use Securimage;
use Securimage_Color;
use Symfony\Component\HttpFoundation\Response;

class SecurimageController extends AbstractController implements CaptchaWithPictureInterface, ConfigurableCaptchaInterface
{
    /**
     * The default ID of the input field.
     *
     * @var string
     */
    const DEFAULT_INPUT_ID = 'ccm-captcha-code';

    /**
     * The default name of the input field.
     *
     * @var string
     */
    const DEFAULT_INPUT_NAME = 'ccmCaptchaCode';

    /**
     * @var ResolverManagerInterface
     */
    protected $urlResolver;

    /**
     * @var FormService
     */
    protected $formService;

    /**
     * @var Securimage
     */
    protected $securimage;

    /**
     * @var array
     */
    protected $labelAttributes = [];

    /**
     * @var array
     */
    protected $pictureAttributes = [];

    /**
     * @var array
     */
    protected $inputAttributes = [];

    /**
     * Initialize the instance.
     *
     * @param ResolverManagerInterface $urlResolver
     * @param FormService $formService
     * @param Request $request
     */
    public function __construct(ResolverManagerInterface $urlResolver, FormService $formService, Request $request)
    {
        $this->request = $request;
        $this->urlResolver = $urlResolver;
        $this->formService = $formService;
        $this->securimage = new Securimage([
            'no_session' => PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg',
            // The picture is returned as a Response (see displayCaptchaPicture()): Securimage must not send the headers nor exit
            'send_headers' => false,
            'no_exit' => true,
        ]);
        $this->securimage->image_width = 190;
        $this->securimage->image_height = 60;
        $this->securimage->image_bg_color = new Securimage_Color('#e3daed');
        $this->securimage->line_color = new Securimage_Color('#333333');
        $this->securimage->num_lines = 5;
        $this->securimage->text_color = new Securimage_Color('#b80432');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\ConfigurableCaptchaInterface::getLabelAttributes()
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\ConfigurableCaptchaInterface::setLabelAttributes()
     */
    public function setLabelAttributes(array $attributes)
    {
        $this->labelAttributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::label()
     */
    public function label()
    {
        $attributes = $this->getLabelAttributes();
        if (array_key_exists('id', $attributes)) {
            $inputID = $attributes['id'];
            unset($attributes['id']);
        } else {
            $inputID = static::DEFAULT_INPUT_ID;
        }
        echo $this->formService->label($inputID, t('Please type the letters and numbers shown in the image. Click the image to see another captcha.'), $attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\ConfigurableCaptchaInterface::setPictureAttributes()
     */
    public function setPictureAttributes(array $attributes)
    {
        $this->pictureAttributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\ConfigurableCaptchaInterface::getPictureAttributes()
     */
    public function getPictureAttributes()
    {
        return $this->pictureAttributes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::display()
     */
    public function display()
    {
        $image = Image::create(
            $this->urlResolver->resolve(['/ccm/system/captcha/picture'])->setQuery('nocache=' . round(microtime(true) * 1000)),
            t('Captcha Code'),
            [
                'class' => 'ccm-captcha-image',
                'onclick' => h('this.src = this.src.replace(/([?&]nocache=)(\d+)/, \'$1\' + ((new Date()).getTime()))'),
                'width' => $this->securimage->image_width,
                'height' => $this->securimage->image_height,
            ]
        );
        foreach ($this->getPictureAttributes() as $attributeName => $attributeValue) {
            if ($attributeValue === null) {
                $image->removeAttribute($attributeName);
            } else {
                switch ($attributeName) {
                    case 'class':
                        $image->addClass($attributeValue);
                        break;
                    default:
                        $image->setAttribute($attributeName, $attributeValue);
                        break;
                }
            }
        }
        echo '<div>', (string) $image, '</div>';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\ConfigurableCaptchaInterface::setInputAttributes()
     */
    public function setInputAttributes(array $attributes)
    {
        $this->inputAttributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\ConfigurableCaptchaInterface::getInputAttributes()
     */
    public function getInputAttributes()
    {
        return $this->inputAttributes;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::showInput()
     */
    public function showInput()
    {
        $attributes = $this->getInputAttributes();

        if (array_key_exists('name', $attributes)) {
            $inputName = $attributes['name'];
            unset($attributes['name']);
        } else {
            $inputName = static::DEFAULT_INPUT_NAME;
        }
        $input = Input::create(
            'text',
            $inputName,
            null,
            [
                'id' => static::DEFAULT_INPUT_ID,
                'class' => 'form-control ccm-input-captcha',
                'required' => 'required',
                'autocomplete' => 'off',
            ]
        );
        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeValue === null) {
                $input->removeAttribute($attributeName);
            } else {
                switch ($attributeName) {
                    case 'class':
                        $input->addClass($attributeValue);
                        break;
                    default:
                        $input->setAttribute($attributeName, $attributeValue);
                        break;
                }
            }
        }
        echo '<div>', (string) $input, '</div><br />';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::check()
     */
    public function check()
    {
        $attributes = $this->getInputAttributes();
        if (array_key_exists('name', $attributes)) {
            $inputName = $attributes['name'];
        } else {
            $inputName = static::DEFAULT_INPUT_NAME;
        }
        $checkCode = $this->request->get($inputName);
        if (!is_string($checkCode)) {
            $checkCode = '';
        }

        return $this->securimage->check($checkCode);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaWithPictureInterface::displayCaptchaPicture()
     */
    public function displayCaptchaPicture()
    {
        switch ($this->securimage->image_type) {
            case Securimage::SI_IMAGE_JPEG:
                $contentType = 'image/jpeg';
                break;
            case Securimage::SI_IMAGE_GIF:
                $contentType = 'image/gif';
                break;
            default:
                $contentType = 'image/png';
                break;
        }
        ob_start();
        try {
            $this->securimage->show();
            $picture = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        return $this->app->make(ResponseFactoryInterface::class)->create($picture, Response::HTTP_OK, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
        ]);
    }
}
