<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $ih = Loader::helper('concrete/ui'); ?>
<?php
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
$form = Loader::helper('form');
?>


<?php if ($this->controller->getTask() == 'edit_importer') { ?>

<form method="post" id="mail-importer-form" action="<?=$view->url('/dashboard/system/mail/importers', 'save_importer')?>">
    <div class="row">
        <div class="col-md-6">
            <?php Loader::helper('validation/token')->output('save_importer') ?>
            <?=$form->hidden('miID', $mi->getMailImporterID())?>
            <fieldset>
                <legend><?=t($mi->getMailImporterName())?> <?=t('Settings');?></legend>
                <div class="form-group">
                    <?=$form->label('miEmail',t('Email Address to Route Emails To'));?>
                    <?=$form->text('miEmail', $mi->getMailImporterEmail())?>
                </div>

                <div class="form-group">
                    <?=$form->label('miIsEnabled',t('Enabled'));?>
                    <?=$form->select('miIsEnabled', $enabledVals, $mi->isMailImporterEnabled())?>
                </div>
            </fieldset>
            <div class="spacer-row-2"></div>
            <fieldset>
                <legend><?=t('POP Mail Server Authentication Settings')?></legend>
                <div class="form-group">
                    <?=$form->label('miServer',t('Mail Server'));?>
                    <?=$form->text('miServer', $mi->getMailImporterServer())?>
                </div>
                <div class="form-group">
                    <?=$form->label('miUsername',t('Username'));?>
                    <?=$form->text('miUsername', $mi->getMailImporterUsername())?>
                </div>
                <div class="form-group">
                    <?=$form->label('miPassword',t('Password'));?>
                    <?=$form->text('miPassword', $mi->getMailImporterPassword())?>
                </div>

                <div class="form-group">
                    <?=$form->label('miEncryption',t('Encryption'));?>
                    <?=$form->select('miEncryption', $secureVals, $mi->getMailImporterEncryption())?>
                </div>
                <?php $port = $mi->getMailImporterPort() == 0 ? '' : $mi->getMailImporterPort(); ?>

                <div class="form-group">
                    <?=$form->label('miPort',t('Port (Leave blank for default)'));?>
                    <?=$form->text('miPort', $port)?>
                </div>

                <div class="form-group">
                    <?=$form->label('miConnectionMethod', t('Connection Method'));?>
                    <?=$form->select('miConnectionMethod', array('POP' => 'POP', 'IMAP' => 'IMAP'), $mi->getMailImporterConnectionMethod())?>
                </div>
            </fieldset>
        </div>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?=$ih->submit(t('Save'), 'mail-importer-form','right', 'btn-primary')?>
            </div>
        </div>
    </div>
</form>
<?php } else { ?>
	<?php if (count($importers) == 0) { ?>
		<p><?=t('There are no mail importers. Mail importers poll email accounts for new messages and run actions on those messages.')?></p>
	<?php } else { ?>
	
	<table class="table table-striped" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?=t('Name')?></td>
		<td class="header"><?=t('Server')?></td>
		<td class="header"><?=t('Email Address')?></td>
		<td class="header"><?=t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php foreach($importers as $mi) { ?>
		<tr>
			<td><?=$mi->getMailImporterName()?></td>
			<td><?=$mi->getMailImporterServer()?></td>
			<td><?=$mi->getMailImporterEmail()?></td>
			<td><?=$mi->isMailImporterEnabled() ? t('Yes') : t('No')?></td>
			<td width="60"><?php
				print $ih->button(t('Edit'), $view->url('/dashboard/system/mail/importers', 'edit_importer', $mi->getMailImporterID()), 'left', 'btn-xs');
			?></td>
		</tr>
	<?php } ?>
	</table>
	<?php } ?>
<?php } ?>
