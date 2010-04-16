<?php 
/**
 * Captcha helper
 * 
 * Can be used within a single page or a block.
 * 
 * Create a form like this.
 * <code>
 * <form method="post">
 *  <?php 
 *  $captcha = Loader::helper('validation/captcha');
 *  $captcha->display();
 *  ?>
 *  <input type="text" name="ccmCaptchaCode"/>
 *  <input type="submit" name="checkcaptcha"/>
 *  </form> 
 * </code>
 *     
 * You can verify the captcha using this code: 
 * <code>
 * $captcha = Loader::helper('validation/captcha');
 * $captcha->check();
 * </code>      
 * 
 * If your captcha field is not named "ccmCaptchaCode", you can specify its name
 * like this: $captcha->check('myFieldName');      
 *  
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Remo Laubacher <remo.laubacher@gmail.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ValidationCaptchaHelper {

   private $securimage;
   
	public function __construct() {
		Loader::library("3rdparty/securimage/securimage");
		
		$this->securimage = new Securimage();
		$this->securimage->ttf_file = DIR_LIBRARIES_3RDPARTY_CORE . '/securimage/elephant.ttf';
	}
	
	/** 
	 * Display the captcha
	 */ 
	public function display() {
	  // @TODO: How do we properly print a picture using a helper function?
	  $ci = Loader::helper('concrete/urls');
      echo '<img src="' . $ci->getToolsURL('captcha') . '?nocache=' .time(). '" alt="Captcha Code" class="ccm-captcha-image" />';      
	}
	
	/** 
	 * Print the captcha image. You usually don't have to call this method directly.
	 * It gets called by captcha.php from the tools 
	 */	 	
	public function displayCaptchaPicture() {
	   $this->securimage->show();
	}
	
	/**
	 * Displays the text input field that must be entered when used with a corresponding image.
	 */
	public function showInput()
	{
	  echo '<input type="text" name="ccmCaptchaCode" class="ccm-input-captcha" />';
	}
	
	/** 
	 * Checks the captcha code the user has entered.
	 *    	 
	 * @param string $fieldName Optional name of the field that contains the captcha code
	 * @return boolean true if the code was correct, false if not
	 */   	
	public function check($fieldName='ccmCaptchaCode') {
	   return $this->securimage->check($_REQUEST[$fieldName]);
	}
	
}

?>
