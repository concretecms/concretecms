
	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Task Permissions'), $help, 'span8 offset2', false)?>
	<form method="post" action="<?=$this->action('save')?>">
	<?=Loader::helper('validation/token')->output('save_permissions')?>
	<div class="ccm-pane-body">
	<?
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<? Loader::element('permission/lists/miscellaneous')?>
	<? } else { ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<? } ?>
	</div>
	<div class="ccm-pane-footer">
		<a href="<?=$this->url('/dashboard/system/permissions/tasks')?>" class="btn"><?=t('Cancel')?></a>
		<button type="submit" value="<?=t('Save')?>" class="btn primary ccm-button-right"><?=t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
	</div>
	</form>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>