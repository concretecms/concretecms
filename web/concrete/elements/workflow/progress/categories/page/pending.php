<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
$list = $category->getPendingWorkflowProgressList();
$items = $list->get();
if (count($items) > 0) { ?>

<table class="ccm-results-list">
<tr>
	<th class="<?=$list->getSearchResultsClass('cvName')?>"><a href="<?=$list->getSortByURL('cvName', 'asc')?>"><?=t('Page Name')?></a></th>
	<th><?=t('URL')?></th>
	<th class="<?=$list->getSearchResultsClass('wpDateLastAction')?>"><a href="<?=$list->getSortByURL('wpDateLastAction', 'desc')?>"><?=t('Last Action')?></a></th>
	<th class="<?=$list->getSearchResultsClass('wpCurrentStatus')?>"><a href="<?=$list->getSortByURL('wpCurrentStatus', 'desc')?>"><?=t('Current Status')?></a></th>
	<th>&nbsp;</th>
</tr>
<? 
$noitems = true;
	foreach($items as $it) { 
	$p = $it->getPageObject();
	$wp = $it->getWorkflowProgressObject();
	$wf = $wp->getWorkflowObject();
	if ($wf->canApproveWorkflowProgressObject($wp)) { 
		$noitems = false;
	?>
<tr>
	<td><?=$p->getCollectionName()?></td>
	<td><a href="<?=Loader::helper('navigation')->getLinkToCollection($p)?>"><?=$p->getCollectionPath()?></a>
	<td><?=date(DATE_APP_GENERIC_MDYT_FULL, strtotime($wp->getWorkflowProgressDateLastAction()))?></td>
	<td><?=$wf->getWorkflowProgressStatusDescription($wp)?></td>
	<td>
	<? $actions = $wp->getWorkflowProgressActions(); ?>
	<? foreach($actions as $act) { ?>
		<a href="<?=$this->action('workflow_action', $category->getWorkflowProgressCategoryHandle(), $wp->getWorkflowProgressID(), $act->getWorkflowProgressActionTask(), Loader::helper('validation/token')->generate())?>" class="btn <?=$act->getWorkflowProgressActionStyleClass()?>"><?=$act->getWorkflowProgressActionLabel()?></a>
	<? } ?>
	</td>
<? } 

} ?>
<? if ($noitems) { ?>
	<tr>
		<td colspan="5"><?=t('There is nothing currently waiting for you.')?></td>
	</tr>
<? } ?>
</table>

<? } else { ?>
	<p>None.</p>
<? } ?>