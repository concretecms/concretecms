<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Waiting for Me'), false)?>

<?=Loader::helper('concrete/ui')->tabs($tabs, false); ?>
	
<? if ($category->getPackageID() > 0) { ?>
	<? Loader::packageElement('workflow/progress/categories/' . $category->getWorkflowProgressCategoryHandle() . '/pending', $category->getPackageHandle(), array('category' => $category))?>
<? } else { ?>
	<? Loader::element('workflow/progress/categories/' . $category->getWorkflowProgressCategoryHandle() . '/pending', array('category' => $category)); ?>
<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
