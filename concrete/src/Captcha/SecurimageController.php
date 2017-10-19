<?php

namespace Concrete\Core\Captcha;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Form\Service\Form as FormService;
use Concrete\Core\Http\Request;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use HtmlObject\Image;
use HtmlObject\Input;
use Securimage;
use Securimage_Color;

class SecurimageController extends AbstractController implements CaptchaWithPictureInterface
{
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
        $this->securimage = new Securimage();
        $this->securimage->image_width = 190;
        $this->securimage->image_height = 60;
        $this->securimage->image_bg_color = new Securimage_Color(227, 218, 237);
        $this->securimage->line_color = new Securimage_Color(51, 51, 51);
        $this->securimage->num_lines = 5;

        $this->securimage->use_multi_text = true;
        $this->securimage->multi_text_color = [
            new Securimage_Color(184, 4, 50),
            new Securimage_Color(12, 67, 157),
            new Securimage_Color(244, 49, 11),
        ];
        $this->securimage->text_color = new Securimage_Color(184, 4, 50);
    }

    /**
     * {@inheritdoc}
     */
    public function display(array $customImageAttributes = [])
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
        foreach ($customImageAttributes as $customAttributeName => $customAttributeValue) {
            if ($customAttributeValue === null) {
                $image->removeAttribute($customAttributeName);
            } else {
                switch ($customAttributeName) {
                    case 'class':
                        $image->addClass($customAttributeValue);
                        break;
                    default:
                        $image->setAttribute($customAttributeName, $customAttributeValue);
                        break;
                }
            }
        }
        echo '<div>', (string) $image, '</div>';
    }

    /**
     * {@inheritdoc}
     */
    public function label($inputID = 'ccm-captcha-code')
    {
        echo $this->formService->label($inputID, t('Please type the letters and numbers shown in the image. Click the image to see another captcha.'));
    }

    /**
     * {@inheritdoc}
     */
    public function showInput(array $customInputAttributes = [])
    {
        $input = Input::create(
            'text',
            'ccmCaptchaCode',
            null,
            [
                'id' => 'ccm-captcha-code',
                'class' => 'form-control ccm-input-captcha',
                'required' => 'required',
            ]
        );
        foreach ($customInputAttributes as $customAttributeName => $customAttributeValue) {
            if ($customAttributeValue === null) {
                $input->removeAttribute($customAttributeName);
            } else {
                switch ($customAttributeName) {
                    case 'class':
                        $input->addClass($customAttributeValue);
                        break;
                    default:
                        $image->setAttribute($customAttributeName, $customAttributeValue);
                        break;
                }
            }
        }
        echo '<div>', (string) $input, '</div><br />';
    }

    /**
     * {@inheritdoc}
     */
    public function check($fieldName = 'ccmCaptchaCode')
    {
        return $this->securimage->check($this->request->get($fieldName));
    }

    /**
     * {@inheritdoc}
     */
    public function displayCaptchaPicture()
    {
        $this->securimage->show();
    }
}
