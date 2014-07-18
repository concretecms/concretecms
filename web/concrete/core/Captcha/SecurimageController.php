<?php
namespace Concrete\Core\Captcha;

use Loader;
use \Concrete\Core\Foundation\Object;
use Securimage;
use Securimage_Color;

class SecurimageController extends Controller
{
    protected $securimage;

    public function __construct()
    {
        $this->securimage = new Securimage();
        $this->securimage->image_width   = 190;
        $this->securimage->image_height  = 60;
        $this->securimage->image_bg_color = new Securimage_Color(227, 218, 237);
        $this->securimage->line_color = new Securimage_Color(51, 51, 51);
        $this->securimage->num_lines = 5;

        $this->securimage->use_multi_text   = true;
        $this->securimage->multi_text_color = array(
            new Securimage_Color(184, 4, 50),
            new Securimage_Color(12, 67, 157),
            new Securimage_Color(244, 49, 11)
        );
        $this->securimage->text_color = new Securimage_Color(184, 4, 50);
    }

    /**
     * Display the captcha
     */
    public function display()
    {
        $ci = Loader::helper('concrete/urls');
        echo '<div><img src="' . $ci->getToolsURL('captcha') . '?nocache=' .time(). '" alt="' .t('Captcha Code'). '" onclick="this.src = \'' . $ci->getToolsURL('captcha') . '?nocache=\'+(new Date().getTime())" class="ccm-captcha-image" /></div>';
        echo '<br/><div>' . t('Click the image to see another captcha.') . '</div>';
    }

    public function label()
    {
        $form = Loader::helper('form');
        print $form->label('captcha', t('Please type the letters and numbers shown in the image.'));
    }

    /**
     * Print the captcha image. You usually don't have to call this method directly.
     * It gets called by captcha.php from the tools
     */
    public function displayCaptchaPicture()
    {
        $this->securimage->show();
    }

    /**
     * Displays the text input field that must be entered when used with a corresponding image.
     */
    public function showInput($args = false)
    {
        $attribs = '';
        if (is_array($args)) {
            foreach ($args as $key => $value) {
                $attribs .= $key . '="' . $value . '" ';
            }
        }
        echo '<div><input type="text" name="ccmCaptchaCode" class="ccm-input-captcha" required="required" ' . $attribs . ' /></div><br/>';
    }

    /**
     * Checks the captcha code the user has entered.
     *
     * @param string $fieldName Optional name of the field that contains the captcha code
     * @return boolean true if the code was correct, false if not
     */
    public function check($fieldName='ccmCaptchaCode')
    {
        return $this->securimage->check($_REQUEST[$fieldName]);
    }

}
