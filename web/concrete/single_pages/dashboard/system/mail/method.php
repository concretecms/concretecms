<? $ih = Loader::helper('concrete/interface'); ?>
<?
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
$form = Loader::helper('form');
?>
	

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('SMTP Method'), false, 'span8 offset2', false)?>
	<form method="post" action="<?=$this->url('/dashboard/system/mail/method', 'save_settings')?>" class="form-horizontal" id="mail-settings-form">
	<?php Loader::helper('validation/token')->output('save_settings') ?>
	<div class="ccm-pane-body">
	<fieldset>
	<legend><?=t('Send Mail Method')?></legend>
	<div class="control-group">
	<div class="controls">
	<label class="radio"><?=$form->radio('MAIL_SEND_METHOD', 'PHP_MAIL', MAIL_SEND_METHOD)?> <span><?=t('Default PHP Mail Function')?></span></label>
	<label class="radio"><?=$form->radio('MAIL_SEND_METHOD', 'SMTP', MAIL_SEND_METHOD)?> <span><?=t('External SMTP Server')?></span></label>
	</div>
	</div>
	</fieldset>
	<fieldset id="ccm-settings-mail-smtp">
		<legend><?=t('SMTP Settings')?></legend>
			<div class="control-group">
				<?=$form->label('MAIL_SEND_METHOD_SMTP_SERVER',t('Mail Server'));?>
				<div class="controls">
					<?=$form->text('MAIL_SEND_METHOD_SMTP_SERVER', Config::get('MAIL_SEND_METHOD_SMTP_SERVER'))?>
				</div>
			</div>
			<div class="control-group">
				<?=$form->label('MAIL_SEND_METHOD_SMTP_USERNAME',t('Username'));?>
				<div class="controls">
					<?=$form->text('MAIL_SEND_METHOD_SMTP_USERNAME', Config::get('MAIL_SEND_METHOD_SMTP_USERNAME'))?>
				</div>
			</div>
			<div class="control-group">
				<?=$form->label('MAIL_SEND_METHOD_SMTP_PASSWORD',t('Password'));?>
				<div class="controls">
					<?=$form->password('MAIL_SEND_METHOD_SMTP_PASSWORD', Config::get('MAIL_SEND_METHOD_SMTP_PASSWORD'), array('autocomplete' => 'off'))?>
				</div>
			</div>
			
			<div class="control-group">
				<?=$form->label('MAIL_SEND_METHOD_SMTP_ENCRYPTION',t('Encryption'));?>
				<div class="controls">
					<?=$form->select('MAIL_SEND_METHOD_SMTP_ENCRYPTION', $secureVals, Config::get('MAIL_SEND_METHOD_SMTP_ENCRYPTION'))?>
				</div>
			</div>
			<div class="control-group">
				<?=$form->label('MAIL_SEND_METHOD_SMTP_PORT',t('Port (Leave blank for default)'));?>
				<div class="controls">
					<?=$form->text('MAIL_SEND_METHOD_SMTP_PORT', Config::get('MAIL_SEND_METHOD_SMTP_PORT'))?>
				</div>
			</div>	
	</fieldset>	
	</div>
	<div class="ccm-pane-footer">
		<?=$ih->submit(t('Save'), 'mail-settings-form','right','primary')?>
	</div>
	</form>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
	
		<script type="text/javascript">
	ccm_checkMailSettings = function() {
		obj = $("input[name=MAIL_SEND_METHOD]:checked");
		if (obj.val() == 'SMTP') {
			$("#ccm-settings-mail-smtp").show();	
		} else {
			$("#ccm-settings-mail-smtp").hide();	
		}
	}

	$(function() {
		$("input[name=MAIL_SEND_METHOD]").click(function() {
			ccm_checkMailSettings();
		});
		ccm_checkMailSettings();	
	});

	</script>
