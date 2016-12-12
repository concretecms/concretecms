<? defined('C5_EXECUTE') or die("Access Denied."); ?>

	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	<? $fs = FileSet::getGlobal(); ?>
		<form method="post" action="<?=$view->action('save')?>" id="ccm-permission-list-form">
	
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	<div class="ccm-pane-body">
	<?
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<? Loader::element('permission/lists/file_set', array('fs' => $fs))?>
	<? } else { ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<? } ?>
	</div>
	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=$view->url('/dashboard/system/files/permissions')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
	</form>
