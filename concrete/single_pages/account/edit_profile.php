<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<h2><?=t($c->getCollectionName())?></h2>

<form method="post" action="<?php echo $view->action('save')?>" enctype="multipart/form-data">
	<?php  $attribs = UserAttributeKey::getEditableInProfileList();
    $valt->output('profile_edit');
    ?>
	<fieldset>
	<legend><?=t('Basic Information')?></legend>
	<div class="form-group">
		<?php echo $form->label('uEmail', t('Email'))?>
		<?php echo $form->text('uEmail', $profile->getUserEmail())?>
	</div>
	<?php  if (Config::get('concrete.misc.user_timezones')) {
     ?>
		<div class="form-group">
			<?php echo  $form->label('uTimezone', t('Time Zone'))?>
			<?php echo  $form->select('uTimezone',
                Core::make('helper/date')->getTimezones(),
                ($profile->getUserTimezone() ? $profile->getUserTimezone() : date_default_timezone_get())
        );
     ?>
		</div>
	<?php
 } ?>
	<?php  if (is_array($locales) && count($locales)) {
     ?>
		<div class="form-group">
			<?php echo $form->label('uDefaultLanguage', t('Language'))?>
			<?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale())?>
		</div>
	<?php
 } ?>
	<?php
    if (is_array($attribs) && count($attribs)) {
        foreach ($attribs as $ak) {
            echo $profileFormRenderer
                ->buildView($ak)
                ->setIsRequired($ak->isAttributeKeyRequiredOnProfile())
                ->render();
        }
    }
    ?>
	</fieldset>
	<?php
    $ats = [];
    foreach (AuthenticationType::getList(true, true) as $at) {
        /* @var AuthenticationType $at */
        if ($at->isHooked($profile)) {
            if ($at->hasHooked()) {
                $ats[] = [$at, 'renderHooked'];
            }
        } else {
            if ($at->hasHook()) {
                $ats[] = [$at, 'renderHook'];
            }
        }
    }

    if (!empty($ats)) {
        ?>
		<fieldset>
			<legend><?=t('Authentication Types')?></legend>
			<?php
            foreach ($ats as $at) {
                call_user_func($at);
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
            <?php echo $form->password('uPasswordNew', array('autocomplete' => 'off'))?>
            <a href="javascript:void(0)" title="<?=t("Leave blank to keep current password.")?>"><i class="icon-question-sign"></i></a>
		</div>

        <div class="form-group">
            <?php echo $form->label('uPasswordNewConfirm', t('Confirm New Password'))?>
            <div class="controls">
                <?php echo $form->password('uPasswordNewConfirm', array('autocomplete' => 'off'))?>
            </div>
        </div>

	</fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
    		<input type="submit" name="save" value="<?=t('Save')?>" class="btn btn-primary pull-right" />
        </div>
	</div>

</form>
