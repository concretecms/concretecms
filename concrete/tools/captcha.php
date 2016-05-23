<?php

defined('C5_EXECUTE') or die("Access Denied.");

//this was added because the captcha wasn't printing when C5 was in development mode, because it 
//allegedly 'contained errors' (probably some kind of suppressed warning) even though it worked 
//fine while in production mode.  you may need to uncomment this for debugging.

ini_set('error_reporting', 0);
ini_set('display_errors', 0);
error_reporting(0);

$captcha = Loader::helper('validation/captcha');
$captcha->displayCaptchaPicture();
