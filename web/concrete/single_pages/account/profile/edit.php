<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<script type="text/javascript">
$(function() {
	$('i.icon-question-sign').parent().tooltip();
});
</script>

<div class="row">
<div class="span10 offset1">

	<h1 class="page-header"><?=t('Edit Profile')?></h1>

	<form method="post" action="<?php echo $this->action('save')?>" enctype="multipart/form-data">
	<?php  $attribs = UserAttributeKey::getEditableInProfileList(); 
	if(is_array($attribs) && count($attribs)) { 
	?>
		<legend><?=t('Basic Information')?></legend>
		<fieldset>
		<div class="control-group">
			<?php echo $form->label('uEmail', t('Email'))?>
			<div class="controls">
				<?php echo $form->text('uEmail',$profile->getUserEmail())?>					
			</div>
		</div>
		<?php  if(ENABLE_USER_TIMEZONES) { ?>
			<div class="control-group">
				<?php echo  $form->label('uTimezone', t('Time Zone'))?>
				<div class="controls">
				<?php echo  $form->select('uTimezone', 
					$date->getTimezones(), 
					($profile->getUserTimezone()?$profile->getUserTimezone():date_default_timezone_get())
			); ?>
				</div>
			</div>
		<?php  } ?>               
		<?php 
		$af = Loader::helper('form/attribute');
		$af->setAttributeObject($profile);
		foreach($attribs as $ak) {
			print '<div class="ccm-profile-attribute">';
			print $af->display($ak, $ak->isAttributeKeyRequiredOnProfile());
			print '</div>';
		} ?>
		</fieldset>
	<?php  } ?>
	
	<legend><?=t('Change Password')?></legend>
	<fieldset>
	<div class="control-group">
		<?php echo $form->label('uPasswordNew', t('New Password'))?>
		<div class="controls">
			<?php echo $form->password('uPasswordNew')?>
			<a href="javascript:void(0)" title="<?=t("Leave blank to keep current password.")?>"><i class="icon-question-sign"></i></a>
		</div>
	</div>
	
	<div class="control-group">
		<?php echo $form->label('uPasswordNewConfirm', t('Confirm New Password'))?>
		<div class="controls">
			<?php echo $form->password('uPasswordNewConfirm')?>
		</div>
	</div>
	
	</fieldset>
	
	<div class="form-actions">
		<a href="<?=DIR_REL?>/" class="btn" /><?=t('Back to Home')?></a>
		<input type="submit" name="save" value="<?=t('Save')?>" class="btn btn-primary pull-right" />
	</div>
	
	</form>

</div>
</div>