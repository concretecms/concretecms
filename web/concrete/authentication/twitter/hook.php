<?php
defined('C5_EXECUTE') or die('Access Denied');
?>
<a href="<?= \URL::to('/system/authentication/twitter/attempt_attach'); ?>">
    <img src="<?php echo ASSETS_URL_IMAGES.'/authentication/twitter/sign-in-with-twitter-link.png';?>?>"
         alt="<?php echo t('Login With Twitter')?>"/>
</a>
