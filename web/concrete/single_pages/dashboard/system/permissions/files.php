
	<? ob_start(); ?>
	<?=Loader::element('permission/help');?>
	<? $help = ob_get_contents(); ?>
	<? ob_end_clean(); ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Manager Permissions'), $help, 'span12 offset2')?>

	<? $fs = FileSet::getGlobal(); ?>
	<? Loader::element('permission/lists/file_set', array('fs' => $fs))?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>