
	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Task Permissions'), $help, 'span12 offset2')?>
	
	<?
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>
	
		<? Loader::element('permission/lists/miscellaneous')?>
	
	<? } else { ?>
		<p><?=t('You cannot access task permissions.')?></p>
	<? } ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>