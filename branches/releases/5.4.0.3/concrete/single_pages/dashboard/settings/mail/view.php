<div style="width: 680px">

<?php  $ih = Loader::helper('concrete/interface'); ?>
<?php 
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
$form = Loader::helper('form');
?>


<?php  if ($this->controller->getTask() == 'edit_importer') { ?>
	
	<h1><span><?php echo t('Edit Importer')?></span></h1>
	<div class="ccm-dashboard-inner">

	<form method="post" id="mail-importer-form" action="<?php echo $this->url('/dashboard/settings/mail', 'save_importer')?>">
	<?php echo $form->hidden('miID', $mi->getMailImporterID())?>
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0" width="600">
	<tr>
		<td class="header" style="width: 25%"><?php echo t('Name')?></td>
		<td class="header" style="width: 60%"><?php echo t('Email Address to Route Emails To')?></td>
		<td class="header" style="width: 15%"><?php echo t('Enabled')?></td>
	</tr>
	<tr>
		<td><strong><?php echo $mi->getMailImporterName()?></strong></td>
		<td><?php echo $form->text('miEmail', $mi->getMailImporterEmail(), array('style' => 'width: 350px'))?></td>
		<td><?php echo $form->select('miIsEnabled', $enabledVals, $mi->isMailImporterEnabled())?></td>
	</tr>
	</table>

	<br/>
	
	<h2><?php echo t('POP Mail Server Authentication Settings')?></h2>
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header" width="33%"><?php echo t('Mail Server')?></td>
		<td class="header" width="34%"><?php echo t('Username')?></td>
		<td class="header" width="33%"><?php echo t('Password')?></td>
	</tr>
	<tr>
		<td><?php echo $form->text('miServer', $mi->getMailImporterServer(), array('style' => 'width: 100%'))?></td>
		<td><?php echo $form->text('miUsername', $mi->getMailImporterUsername(), array('style' => 'width: 100%'))?></td>
		<td><?php echo $form->text('miPassword', $mi->getMailImporterPassword(), array('style' => 'width: 100%'))?></td>
	</tr>
	<tr>
		<td class="header"><?php echo t('Encryption')?></td>
		<td class="header"><?php echo t('Port (Leave blank for default)')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php  $port = $mi->getMailImporterPort() == 0 ? '' : $mi->getMailImporterPort(); ?>
	<tr>
		<td><?php echo $form->select('miEncryption', $secureVals, $mi->getMailImporterEncryption())?></td>
		<td><?php echo $form->text('miPort', $port)?></td>
		<td>&nbsp;</td>
	</tr>
	</table>
	
	<?php echo $ih->submit(t('Save'), 'mail-importer-form')?>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	</form>
	</div>
	
<?php  } else { ?>
	
	<h1><span><?php echo t('Sitewide Mail Settings')?></span></h1>
	<div class="ccm-dashboard-inner">
	<form method="post" action="<?php echo $this->action('save_settings')?>" id="mail-settings-form">
	
	<h2><?php echo t('Send Mail Method')?></h2>
	<div class="ccm-dashboard-radio">
		<?php echo $form->radio('MAIL_SEND_METHOD', 'PHP_MAIL', MAIL_SEND_METHOD)?> <?php echo t('Default PHP Mail Function')?>
		<?php echo $form->radio('MAIL_SEND_METHOD', 'SMTP', MAIL_SEND_METHOD)?> <?php echo t('External SMTP Server')?>
	</div>
	
	<div id="ccm-settings-mail-smtp">
	<h3><?php echo t('SMTP Settings')?></h3>

	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header" width="33%"><?php echo t('Mail Server')?></td>
		<td class="header" width="34%"><?php echo t('Username')?></td>
		<td class="header" width="33%"><?php echo t('Password')?></td>
	</tr>
	<tr>
		<td><?php echo $form->text('MAIL_SEND_METHOD_SMTP_SERVER', Config::get('MAIL_SEND_METHOD_SMTP_SERVER'), array('style' => 'width: 100%'))?></td>
		<td><?php echo $form->text('MAIL_SEND_METHOD_SMTP_USERNAME', Config::get('MAIL_SEND_METHOD_SMTP_USERNAME'),  array('style' => 'width: 100%'))?></td>
		<td><?php echo $form->password('MAIL_SEND_METHOD_SMTP_PASSWORD', Config::get('MAIL_SEND_METHOD_SMTP_PASSWORD'),  array('style' => 'width: 100%'))?></td>
	</tr>
	<tr>
		<td class="header"><?php echo t('Encryption')?></td>
		<td class="header"><?php echo t('Port (Leave blank for default)')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<tr>
		<td><?php echo $form->select('MAIL_SEND_METHOD_SMTP_ENCRYPTION', $secureVals, Config::get('MAIL_SEND_METHOD_SMTP_ENCRYPTION'), array('style' => 'width: 100%'))?></td>
		<td><?php echo $form->text('MAIL_SEND_METHOD_SMTP_PORT', Config::get('MAIL_SEND_METHOD_SMTP_PORT'),  array('style' => 'width: 100%'))?></td>
		<td>&nbsp;</td>
	</tr>
	</table>
	
	</div>
	
	<?php echo $ih->submit(t('Save'), 'mail-settings-form')?>
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
	
	<h1><span><?php echo t('Mail Importers')?></span></h1>
	<div class="ccm-dashboard-inner">

	<?php  if (count($importers) == 0) { ?>
		<p><?php echo t('There are no mail importers. Mail importers poll email accounts for new messages and run actions on those messages.')?></p>
	<?php  } else { ?>
	
	<table class="grid-list" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?php echo t('Name')?></td>
		<td class="header"><?php echo t('Server')?></td>
		<td class="header"><?php echo t('Email Address')?></td>
		<td class="header"><?php echo t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php  foreach($importers as $mi) { ?>
		<tr>
			<td><?php echo $mi->getMailImporterName()?></td>
			<td><?php echo $mi->getMailImporterServer()?></td>
			<td><?php echo $mi->getMailImporterEmail()?></td>
			<td><?php echo $mi->isMailImporterEnabled() ? t('Yes') : t('No')?></td>
			<td width="60"><?php 
				print $ih->button(t('Edit'), $this->url('/dashboard/settings/mail', 'edit_importer', $mi->getMailImporterID()), 'left');		
			?>
		</tr>
	<?php  } ?>
	</table>
	<?php  } ?>
	
	</div>

<?php  } ?>

</div>