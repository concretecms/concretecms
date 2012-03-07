
	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Permissions'), $help, 'span12 offset2')?>

	<? Loader::element('permission/lists/user')?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>