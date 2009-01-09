<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?=t('Title')?><br />
<input type="text" name="title" value="<?=$title?>" /><br /><br />

<?=t('Comments Require Moderator Approval?')?><br/>
<input type="radio" name="requireApproval" value="1" <?=($requireApproval?"checked=\"checked\"":"") ?> /> <?=t('Yes')?><br />
<input type="radio" name="requireApproval" value="0" <?=($requireApproval?"":"checked=\"checked\"") ?> /> <?=t('No')?><br /><br />

<?=t('Posting Comments is Enabled?')?><br/>
<input type="radio" name="displayGuestBookForm" value="1" <?=($displayGuestBookForm?"checked=\"checked\"":"") ?> /> <?=t('Yes')?><br />
<input type="radio" name="displayGuestBookForm" value="0" <?=($displayGuestBookForm?"":"checked=\"checked\"") ?> /> <?=t('No')?><br /><br />

<?=t('Authentication Required to Post')?><br/>
<input type="radio" name="authenticationRequired" value="0" <?=($authenticationRequired?"":"checked=\"checked\"") ?> /> <?=t('Email Only')?><br />
<input type="radio" name="authenticationRequired" value="1" <?=($authenticationRequired?"checked=\"checked\"":"") ?> /> <?=t('Users must login to C5')?><br /><br />

<?=t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha')?><br/>
<input type="radio" name="displayCaptcha" value="1" <?php echo ($displayCaptcha?"checked=\"checked\"":"") ?> /><?php echo t('Yes')?><br />
<input type="radio" name="displayCaptcha" value="0" <?php echo ($displayCaptcha?"":"checked=\"checked\"") ?> /> <?php echo t('No')?><br /><br />

<?=t('Alert Email Address when Comment Posted')?><br/>
<input name="notifyEmail" type="text" value="<?=$notifyEmail?>" size="30" /><br /><br />
