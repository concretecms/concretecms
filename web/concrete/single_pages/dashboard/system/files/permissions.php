<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<?php $help = ob_get_contents(); ?>
	<?php ob_end_clean(); ?>
	<?php $fs = FileSet::getGlobal(); ?>
		<form method="post" action="<?=$view->action('save')?>" id="ccm-permission-list-form">
	
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	<div class="ccm-pane-body">
	<?
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<?php Loader::element('permission/lists/file_set', array('fs' => $fs))?>
	<?php } else { ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<?php } ?>
	</div>
	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/system/files/permissions')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
	</form>
