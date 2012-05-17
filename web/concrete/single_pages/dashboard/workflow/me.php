<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Waiting for Me'), false)?>
<style type="text/css">
.ccm-results-list td a.btn {
	font-size: 11px;
	line-height: 11px;
	padding: 5px 8px 5px 8px;
}
</style>

<?=Loader::helper('concrete/interface')->tabs($tabs, false); ?>
	
<? if ($category->getPackageID() > 0) { ?>
	<? Loader::packageElement('workflow/progress/categories/' . $category->getWorkflowProgressCategoryHandle() . '/pending', $category->getPackageHandle(), array('category' => $category))?>
<? } else { ?>
	<? Loader::element('workflow/progress/categories/' . $category->getWorkflowProgressCategoryHandle() . '/pending', array('category' => $category)); ?>
<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
