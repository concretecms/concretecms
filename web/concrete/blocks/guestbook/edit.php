<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div class="control-group">
<?=$form->label('title', t('Title'))?>
<div class="controls">
	<input type="text" name="title" value="<?=$title?>" />
</div>
</div>
<?
if (!$dateFormat) {
	$dateFormat = t('M jS, Y');
}
?>

<div class="control-group">
<?=$form->label('dateFormat', t('Date Format'))?>
<div class="controls">
<input type="text" name="dateFormat" value="<?=$dateFormat?>" />
<div class="help-block">(<?=t('Enter a <a href="%s" target="_blank">PHP date string</a> here.', 'http://www.php.net/date')?>)</div>
</div>
</div>

<div class="control-group">
<?=$form->label('displayGuestBookForm', t('Comments enabled.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="displayGuestBookForm" value="1" <?=($displayGuestBookForm?"checked=\"checked\"":"") ?> /> <span><?=t('Yes')?></span></label>
	<label class="radio"><input type="radio" name="displayGuestBookForm" value="0" <?=($displayGuestBookForm?"":"checked=\"checked\"") ?> /> <span><?=t('No')?></span></label>

</div>
</div>

<div class="control-group">
<?=$form->label('requireApproval', t('Comments require approval.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="requireApproval" value="1" <?=($requireApproval?"checked=\"checked\"":"") ?> /> <span><?=t('Yes')?></span></label>
	<label class="radio"><input type="radio" name="requireApproval" value="0" <?=($requireApproval?"":"checked=\"checked\"") ?> /> <span><?=t('No')?></span></label>

</div>
</div>

<div class="control-group">
<?=$form->label('authenticationRequired', t('Authentication required.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="authenticationRequired" value="0" <?=($authenticationRequired?"":"checked=\"checked\"") ?> /> <span><?=t('Email Only')?></span></label>
	<label class="radio"><input type="radio" name="authenticationRequired" value="1" <?=($authenticationRequired?"checked=\"checked\"":"") ?> /> <span><?=t('Users must login')?></span></label>

</div>
</div>

<div class="control-group">
<?=$form->label('displayCaptcha', t('CAPTCHA Required.'))?>
<div class="controls">
	<label class="radio"><input type="radio" name="displayCaptcha" value="1" <?php echo ($displayCaptcha?"checked=\"checked\"":"") ?> /> <span><?=t('Yes')?></span></label>
	<label class="radio"><input type="radio" name="displayCaptcha" value="0" <?php echo ($displayCaptcha?"":"checked=\"checked\"") ?> /> <span><?=t('No')?></span></label>

</div>
</div>

<div class="control-group">
<?=$form->label('notifyEmail', t('Notify Email on Comment'))?>
<div class="controls">
<input type="text" name="notifyEmail" value="<?=$notifyEmail?>" />
</div>
</div>