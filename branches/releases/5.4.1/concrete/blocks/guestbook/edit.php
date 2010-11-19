<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php echo t('Title')?><br />
<input type="text" name="title" value="<?php echo $title?>" /><br /><br />
<?php 
if (!$dateFormat) {
	$dateFormat = t('M jS, Y');
}
?>
<?php echo t('Date Format')?><br/>
<input type="text" name="dateFormat" value="<?php echo $dateFormat?>" />
<div class="ccm-note">(<?php echo t('Enter a <a href="%s" target="_blank">PHP date string</a> here.', 'http://www.php.net/date')?>)</div>
<br/>

<?php echo t('Comments Require Moderator Approval?')?><br/>
<input type="radio" name="requireApproval" value="1" <?php echo ($requireApproval?"checked=\"checked\"":"") ?> /> <?php echo t('Yes')?><br />
<input type="radio" name="requireApproval" value="0" <?php echo ($requireApproval?"":"checked=\"checked\"") ?> /> <?php echo t('No')?><br /><br />

<?php echo t('Posting Comments is Enabled?')?><br/>
<input type="radio" name="displayGuestBookForm" value="1" <?php echo ($displayGuestBookForm?"checked=\"checked\"":"") ?> /> <?php echo t('Yes')?><br />
<input type="radio" name="displayGuestBookForm" value="0" <?php echo ($displayGuestBookForm?"":"checked=\"checked\"") ?> /> <?php echo t('No')?><br /><br />

<?php echo t('Authentication Required to Post')?><br/>
<input type="radio" name="authenticationRequired" value="0" <?php echo ($authenticationRequired?"":"checked=\"checked\"") ?> /> <?php echo t('Email Only')?><br />
<input type="radio" name="authenticationRequired" value="1" <?php echo ($authenticationRequired?"checked=\"checked\"":"") ?> /> <?php echo t('Users must login to C5')?><br /><br />

<?php echo t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha')?><br/>
<input type="radio" name="displayCaptcha" value="1" <?php  echo ($displayCaptcha?"checked=\"checked\"":"") ?> /><?php  echo t('Yes')?><br />
<input type="radio" name="displayCaptcha" value="0" <?php  echo ($displayCaptcha?"":"checked=\"checked\"") ?> /> <?php  echo t('No')?><br /><br />

<?php echo t('Alert Email Address when Comment Posted')?><br/>
<input name="notifyEmail" type="text" value="<?php echo $notifyEmail?>" size="30" /><br /><br />
