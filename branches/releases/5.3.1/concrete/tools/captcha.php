<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$captcha = Loader::helper('validation/captcha');
$captcha->displayCaptchaPicture();

?>
