<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<script type="text/javascript">
$(function() {
	$('i.icon-question-sign').parent().tooltip();
});
</script>

<div class="row">
<div class="col-sm-offset-1 col-sm-10">

	<h1 class="page-header"><?=t('Edit Profile')?></h1>

	<form method="post" action="<?php echo $view->action('save')?>" enctype="multipart/form-data">
	<?php  $attribs = UserAttributeKey::getEditableInProfileList();
	$valt->output('profile_edit');
	?>
	<fieldset>
	<legend><?=t('Basic Information')?></legend>
	<div class="form-group">
		<?php echo $form->label('uEmail', t('Email'))?>
		<?php echo $form->text('uEmail',$profile->getUserEmail())?>
	</div>
	<?php  if (Config::get('concrete.misc.user_timezones')) { ?>
		<div class="form-group">
			<?php echo  $form->label('uTimezone', t('Time Zone'))?>
			<?php echo  $form->select('uTimezone',
				Core::make('helper/date')->getTimezones(),
				($profile->getUserTimezone()?$profile->getUserTimezone():date_default_timezone_get())
		); ?>
		</div>
	<?php  } ?>
	<?php  if (is_array($locales) && count($locales)) { ?>
		<div class="form-group">
			<?php echo $form->label('uDefaultLanguage', t('Language'))?>
			<?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale())?>
		</div>
	<?php  } ?>
	<?php
	if(is_array($attribs) && count($attribs)) {
		$af = Loader::helper('form/attribute');
		$af->setAttributeObject($profile);
		foreach($attribs as $ak) {
			print '<div class="ccm-profile-attribute">';
			print $af->display($ak, $ak->isAttributeKeyRequiredOnProfile());
			print '</div>';
		}
	}
	?>
	</fieldset>
	<?php
	$ats = AuthenticationType::getList(true, true);

    $ats = array_filter($ats, function(AuthenticationType $type) {
        return $type->hasHook();
    });

	$count = count($ats);
	if ($count) {
		?>
		<fieldset>
			<legend><?=t('Authentication Types')?></legend>
			<?php
			foreach ($ats as $at) {
				$at->renderHook();
			}
			?>
		</fieldset>
		<?php
	}
	?>
        <br/>
	<fieldset>
    	<legend><?=t('Change Password')?></legend>
        <div class="form-group">
            <?php echo $form->label('uPasswordNew', t('New Password'))?>
            <?php echo $form->password('uPasswordNew',array('autocomplete' => 'off'))?>
            <a href="javascript:void(0)" title="<?=t("Leave blank to keep current password.")?>"><i class="icon-question-sign"></i></a>
		</div>

        <div class="form-group">
            <?php echo $form->label('uPasswordNewConfirm', t('Confirm New Password'))?>
            <div class="controls">
                <?php echo $form->password('uPasswordNewConfirm',array('autocomplete' => 'off'))?>
            </div>
        </div>

	</fieldset>

	<div class="form-actions">
		<a href="<?=URL::to('/account')?>" class="btn btn-default" /><?=t('Back to Account')?></a>
		<input type="submit" name="save" value="<?=t('Save')?>" class="btn btn-primary pull-right" />
	</div>

	</form>

</div>
</div>
