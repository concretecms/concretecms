<div style="width: 680px">

<? $ih = Loader::helper('concrete/interface'); ?>
<?
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
?>
<h1><span><?=t('Post Content Through Email')?></span></h1>
<div class="ccm-dashboard-inner">


<? if ($this->controller->getTask() == 'edit') { ?>

	<form method="post" id="mail-importer-form" action="<?=$this->url('/dashboard/mail/importers', 'save')?>">
	<?=$form->hidden('miID', $mi->getMailImporterID())?>
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0" width="600">
	<tr>
		<td class="header" style="width: 25%"><?=t('Name')?></td>
		<td class="header" style="width: 60%"><?=t('Email Address to Route Emails To')?></td>
		<td class="header" style="width: 15%"><?=t('Enabled')?></td>
	</tr>
	<tr>
		<td><strong><?=$mi->getMailImporterName()?></strong></td>
		<td><?=$form->text('miEmail', $mi->getMailImporterEmail(), array('style' => 'width: 350px'))?></td>
		<td><?=$form->select('miIsEnabled', $enabledVals, $mi->isMailImporterEnabled())?></td>
	</tr>
	</table>

	<br/>
	
	<h2><?=t('POP Mail Server Authentication Settings')?></h2>
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header" width="33%"><?=t('Mail Server')?></td>
		<td class="header" width="34%"><?=t('Username')?></td>
		<td class="header" width="33%"><?=t('Password')?></td>
	</tr>
	<tr>
		<td><?=$form->text('miServer', $mi->getMailImporterServer(), array('style' => 'width: 195px'))?></td>
		<td><?=$form->text('miUsername', $mi->getMailImporterUsername(), array('style' => 'width: 195px'))?></td>
		<td><?=$form->text('miPassword', $mi->getMailImporterPassword(), array('style' => 'width: 195px'))?></td>
	</tr>
	<tr>
		<td class="header"><?=t('Encryption')?></td>
		<td class="header"><?=t('Port (Leave blank for default)')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<? $port = $mi->getMailImporterPort() == 0 ? '' : $mi->getMailImporterPort(); ?>
	<tr>
		<td><?=$form->select('miEncryption', $secureVals, $mi->getMailImporterEncryption())?></td>
		<td><?=$form->text('miPort', $port)?></td>
		<td>&nbsp;</td>
	</tr>
	</table>
	
	<?=$ih->submit(t('Save'), 'mail-importer-form')?>
	
	<div class="ccm-spacer">&nbsp;</div>
	
	</form>

<? } else { ?>
	<? if (count($importers) == 0) { ?>
		<p><?=t('There are no mail importers. Mail importers poll email accounts for new messages and run actions on those messages.')?></p>
	<? } else { ?>
	
	<table class="grid-list" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?=t('Name')?></td>
		<td class="header"><?=t('Server')?></td>
		<td class="header"><?=t('Email Address')?></td>
		<td class="header"><?=t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<? foreach($importers as $mi) { ?>
		<tr>
			<td><?=$mi->getMailImporterName()?></td>
			<td><?=$mi->getMailImporterServer()?></td>
			<td><?=$mi->getMailImporterEmail()?></td>
			<td><?=$mi->isMailImporterEnabled() ? t('Yes') : t('No')?></td>
			<td width="1"><?
				print $ih->button(t('Edit'), $this->url('/dashboard/mail/importers', 'edit', $mi->getMailImporterID()), 'left');		
			?>
		</tr>
	<? } ?>
	</table>
	<? } ?>

<? } ?>

</div>
</div>