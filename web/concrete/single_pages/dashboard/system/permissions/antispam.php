<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<form method="post" id="site-form" action="<?=$view->action('update_library')?>">
    <div class="form-group">
		<label for='group_id'><?=t('Spam Whitelist Group')?></label>
		<?=$form->select('group_id', (array)$groups, $whitelistGroup);?>
    </div>
	<?=$this->controller->token->output('update_library')?>
	<?php if (count($libraries) > 0) { ?>

		<div class="form-group">
		<?=$form->label('activeLibrary', t('Active Library'))?>
		<?
		$activeHandle = '';
		if (is_object($activeLibrary)) {
			$activeHandle = $activeLibrary->getSystemAntispamLibraryHandle();
		}
		?>

		<?=$form->select('activeLibrary', $libraries, $activeHandle, array('class' => 'form-control'))?>
		</div>

		<?php if (is_object($activeLibrary)) {
			if ($activeLibrary->hasOptionsForm()) {
				if ($activeLibrary->getPackageID() > 0) {
					Loader::packageElement('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form', $activeLibrary->getPackageHandle());
				} else {
					Loader::element('system/antispam/' . $activeLibrary->getSystemAntispamLibraryHandle() . '/form');
				}
			}
		}
		if (is_object($activeLibrary)) {
			?>
			<fieldset>
				<legend style="margin-bottom: 0"><?=t('Log Settings')?></legend>
				<div class="checkbox">
					<div class="checkbox">
						<label><?=$form->checkbox('ANTISPAM_LOG_SPAM', 1, Config::get('concrete.log.spam'))?> <?=t('Log entries marked as spam.')?></label>
					</div>
					<span class="help-block"><?=t('Logged entries can be found in <a href="%s" style="color: #bfbfbf; text-decoration: underline">Dashboard > Reports > Logs</a>', $view->url('/dashboard/reports/logs'))?></span>
				</div>

				<div class="form-group">
					<label><?=t('Email Notification')?> </label>
					<?=$form->text('ANTISPAM_NOTIFY_EMAIL', Config::get('concrete.spam.notify_email'))?>
					<span class="help-block"><?=t('Any email address in this box will be notified when spam is detected.')?></span>
				</div>
			</fieldset>
			<?php
		}
		?>

	<?php } else { ?>
		<p><?=t('You have no anti-spam libraries installed.')?></p>
	<?php } ?>

	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
		    <?=Loader::helper('concrete/ui')->submit(t('Save'), 'submit', 'right', 'btn-primary')?>
        </div>
	</div>

</form>

<script type="text/javascript">
$(function() {
	$('select[name=activeLibrary]').change(function() {
		$('#site-form').submit();
	});
});
</script>
