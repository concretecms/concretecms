<? $ih = Loader::helper('concrete/interface'); ?>
<?
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
$form = Loader::helper('form');
?>


<? if ($this->controller->getTask() == 'edit_importer') { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Importer'), false, 'span8 offset2', false)?>
<form method="post" id="mail-importer-form" class="form-horizontal" action="<?=$this->url('/dashboard/system/mail/importers', 'save_importer')?>">
<div class="ccm-pane-body">

		<?php Loader::helper('validation/token')->output('save_importer') ?>
		<?=$form->hidden('miID', $mi->getMailImporterID())?>
		<fieldset>
			<legend><?=t($mi->getMailImporterName())?> <?=t('Settings');?></legend>
		
			<div class="control-group">
				<?=$form->label('miEmail',t('Email Address to Route Emails To'));?>
				<div class="controls">
					<?=$form->text('miEmail', $mi->getMailImporterEmail())?>
				</div>
			</div>
			
			<div class="control-group">
				<?=$form->label('miIsEnabled',t('Enabled'));?>
				<div class="controls">
					<?=$form->select('miIsEnabled', $enabledVals, $mi->isMailImporterEnabled())?>
				</div>
			</div>	
		</fieldset>
		<fieldset>
			<legend><?=t('POP Mail Server Authentication Settings')?></legend>
			<div class="control-group">
				<?=$form->label('miServer',t('Mail Server'));?>
				<div class="controls">
					<?=$form->text('miServer', $mi->getMailImporterServer())?>
				</div>
			</div>
			<div class="control-group">
				<?=$form->label('miUsername',t('Username'));?>
				<div class="controls">
					<?=$form->text('miUsername', $mi->getMailImporterUsername())?>
				</div>
			</div>
			<div class="control-group">
				<?=$form->label('miPassword',t('Password'));?>
				<div class="controls">
					<?=$form->text('miPassword', $mi->getMailImporterPassword())?>
				</div>
			</div>
			
			<div class="control-group">
				<?=$form->label('miEncryption',t('Encryption'));?>
				<div class="controls">
					<?=$form->select('miEncryption', $secureVals, $mi->getMailImporterEncryption())?>
				</div>
			</div>
			<? $port = $mi->getMailImporterPort() == 0 ? '' : $mi->getMailImporterPort(); ?>
		
			<div class="control-group">
				<?=$form->label('miPort',t('Port (Leave blank for default)'));?>
				<div class="controls">
					<?=$form->text('miPort', $port)?>
				</div>
			</div>

			<div class="control-group">
				<?=$form->label('miConnectionMethod', t('Connection Method'));?>
				<div class="controls">
					<?=$form->select('miConnectionMethod', array('POP' => 'POP', 'IMAP' => 'IMAP'), $mi->getMailImporterConnectionMethod())?>
				</div>
			</div>

	</fieldset>	
</div>
<div class="ccm-pane-footer">
<?=$ih->submit(t('Save'), 'mail-importer-form','right', 'primary')?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>	

<? } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Mail Importers'), false, 'span8 offset2')?>	
	<div class="ccm-pane-body">
	<? if (count($importers) == 0) { ?>
		<p><?=t('There are no mail importers. Mail importers poll email accounts for new messages and run actions on those messages.')?></p>
	<? } else { ?>
	
	<table class="table table-striped" border="0" cellspacing="1" cellpadding="0">
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
			<td width="60"><?
				print $ih->button(t('Edit'), $this->url('/dashboard/system/mail/importers', 'edit_importer', $mi->getMailImporterID()), 'left');		
			?>
		</tr>
	<? } ?>
	</table>
	<? } ?>
</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
<? } ?>
