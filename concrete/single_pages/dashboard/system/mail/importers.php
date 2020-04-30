<?php defined('C5_EXECUTE') or die("Access Denied.");

$ih = Loader::helper('concrete/ui');
$enabledVals = ['0' => t('No'), '1' => t('Yes')];
$secureVals = ['' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS'];
$form = Loader::helper('form');
?>


<?php if ($this->controller->getTask() == 'edit_importer') { ?>
<form method="post" id="mail-importer-form" action="<?php echo $view->url('/dashboard/system/mail/importers', 'save_importer'); ?>">
    <?php Loader::helper('validation/token')->output('save_importer'); ?>
    <?php echo $form->hidden('miID', $mi->getMailImporterID()); ?>

    <fieldset>
        <legend><?php echo t($mi->getMailImporterName()); ?> <?php echo t('Settings'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('miEmail', t('Email Address to Route Emails To')); ?>
            <?php echo $form->text('miEmail', $mi->getMailImporterEmail()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('miIsEnabled', t('Enabled')); ?>
            <?php echo $form->select('miIsEnabled', $enabledVals, $mi->isMailImporterEnabled()); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?php echo t('POP Mail Server Authentication Settings'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('miServer', t('Mail Server')); ?>
            <?php echo $form->text('miServer', $mi->getMailImporterServer()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('miUsername', t('Username')); ?>
            <?php echo $form->text('miUsername', $mi->getMailImporterUsername()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('miPassword', t('Password')); ?>
            <?php echo $form->text('miPassword', $mi->getMailImporterPassword()); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('miEncryption', t('Encryption')); ?>
            <?php echo $form->select('miEncryption', $secureVals, $mi->getMailImporterEncryption()); ?>
        </div>

        <?php $port = $mi->getMailImporterPort() == 0 ? '' : $mi->getMailImporterPort(); ?>
        <div class="form-group">
            <?php echo $form->label('miPort', t('Port (Leave blank for default)')); ?>
            <?php echo $form->text('miPort', $port); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('miConnectionMethod', t('Connection Method')); ?>
            <?php echo $form->select('miConnectionMethod', ['POP' => 'POP', 'IMAP' => 'IMAP'], $mi->getMailImporterConnectionMethod()); ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $ih->submit(t('Save'), 'mail-importer-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>
<?php
} else {
?>
	<?php if (count($importers) == 0) { ?>
	<p><?php echo t('There are no mail importers. Mail importers poll email accounts for new messages and run actions on those messages.'); ?></p>
	<?php
    } else {
    ?>

	<table class="table table-striped">
    	<tr>
    		<td scope="col" class="header"><?php echo t('Name'); ?></td>
    		<td scope="col" class="header"><?php echo t('Server'); ?></td>
    		<td scope="col" class="header"><?php echo t('Email Address'); ?></td>
    		<td scope="col" class="header"><?php echo t('Enabled'); ?></td>
    		<td scope="col" class="header">&nbsp;</td>
    	</tr>
    	<?php foreach ($importers as $mi) { ?>
		<tr>
			<td scope="row"><?php echo $mi->getMailImporterName(); ?></td>
			<td scope="row"><?php echo $mi->getMailImporterServer(); ?></td>
			<td scope="row"><?php echo $mi->getMailImporterEmail(); ?></td>
			<td scope="row"><?php echo $mi->isMailImporterEnabled() ? t('Yes') : t('No'); ?></td>
			<td scope="row" width="60">
            <?php echo $ih->button(t('Edit'), $view->url('/dashboard/system/mail/importers', 'edit_importer', $mi->getMailImporterID()), 'left', 'btn-secondary btn-sm'); ?>
            </td>
		</tr>
    	<?php } ?>
	</table>
	<?php
    }
    ?>
<?php
}
?>
