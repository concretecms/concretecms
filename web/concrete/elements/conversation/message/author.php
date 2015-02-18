<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
?>
<?php if ($u->isRegistered()) { ?>
	<div class="ccm-conversation-avatar"><?php print Loader::helper('concrete/avatar')->outputUserAvatar($ui)?></div>
<?php } else {
	// non-logged-in posting. ?>
	<div class="form-group">
		<label class="control-label" for="cnvMessageAuthorName"><?=t('Name')?></label>
		<input type="text" class="form-control" name="cnvMessageAuthorName" />
	</div>
	<div class="form-group">
		<label class="control-label" for="cnvMessageAuthorEmail"><?=t('Email Address')?></label>
		<input type="text" class="form-control" name="cnvMessageAuthorEmail" />
	</div>
	<?php
	$captcha = Core::make('captcha');
	?>
	<div class="form-group">
		<?php $captcha->label()?>
		<?php $captcha->showInput();?>
		<?php $captcha->display();?>
	</div>
<?php } ?>
