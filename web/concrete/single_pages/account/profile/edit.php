<? defined('C5_EXECUTE') or die("Access Denied."); ?>

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
			<small><?php echo t("Leave this blank to keep your current password.")?></small>
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
		<input type="submit" name="save" value="<?=t('Save')?>" class="btn btn-primary" />
	</div>
	
	</form>
