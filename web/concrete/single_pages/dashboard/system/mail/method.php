<div style="width: 680px">

<? $ih = Loader::helper('concrete/interface'); ?>
<?
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
$form = Loader::helper('form');
?>
	
	<h1><span><?=t('Sitewide Mail Settings')?></span></h1>
	<div class="ccm-dashboard-inner">
	<form method="post" action="<?=$this->action('save_settings')?>" id="mail-settings-form">
	
	<h2><?=t('Send Mail Method')?></h2>
	<div class="ccm-dashboard-radio">
		<?=$form->radio('MAIL_SEND_METHOD', 'PHP_MAIL', MAIL_SEND_METHOD)?> <?=t('Default PHP Mail Function')?>
		<?=$form->radio('MAIL_SEND_METHOD', 'SMTP', MAIL_SEND_METHOD)?> <?=t('External SMTP Server')?>
	</div>
	
	<div id="ccm-settings-mail-smtp">
	<h3><?=t('SMTP Settings')?></h3>

	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header" width="33%"><?=t('Mail Server')?></td>
		<td class="header" width="34%"><?=t('Username')?></td>
		<td class="header" width="33%"><?=t('Password')?></td>
	</tr>
	<tr>
		<td><?=$form->text('MAIL_SEND_METHOD_SMTP_SERVER', Config::get('MAIL_SEND_METHOD_SMTP_SERVER'), array('style' => 'width: 100%'))?></td>
		<td><?=$form->text('MAIL_SEND_METHOD_SMTP_USERNAME', Config::get('MAIL_SEND_METHOD_SMTP_USERNAME'),  array('style' => 'width: 100%'))?></td>
		<td><?=$form->password('MAIL_SEND_METHOD_SMTP_PASSWORD', Config::get('MAIL_SEND_METHOD_SMTP_PASSWORD'),  array('style' => 'width: 100%'))?></td>
	</tr>
	<tr>
		<td class="header"><?=t('Encryption')?></td>
		<td class="header"><?=t('Port (Leave blank for default)')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<tr>
		<td><?=$form->select('MAIL_SEND_METHOD_SMTP_ENCRYPTION', $secureVals, Config::get('MAIL_SEND_METHOD_SMTP_ENCRYPTION'), array('style' => 'width: 100%'))?></td>
		<td><?=$form->text('MAIL_SEND_METHOD_SMTP_PORT', Config::get('MAIL_SEND_METHOD_SMTP_PORT'),  array('style' => 'width: 100%'))?></td>
		<td>&nbsp;</td>
	</tr>
	</table>
	
	</div>
	
	<?=$ih->submit(t('Save'), 'mail-settings-form','right','primary')?>
	<Div class="ccm-spacer">&nbsp;</div>

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
	</form>
	
	</div>
	
	


</div>