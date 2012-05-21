<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
$list = $category->getPendingWorkflowProgressList();
$items = $list->get();
if (count($items) > 0) { ?>

<table class="ccm-results-list" id="ccm-workflow-waiting-for-me">
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
	<td><a href="javascript:void(0)" title="<?=t('Click for history.')?>" onclick="$(this).parentsUntil('tr').parent().next().show()"><?=$wf->getWorkflowProgressStatusDescription($wp)?></a></td>
	<td class="ccm-workflow-progress-actions">
	<form action="<?=$wp->getWorkflowProgressFormAction()?>">
	<input type="hidden" name="source" value="dashboard" />
	<? $actions = $wp->getWorkflowProgressActions(); ?>
	<? foreach($actions as $act) { 
		$attribs = '';
		$_attribs = $act->getWorkflowProgressActionExtraButtonParameters();
		foreach($_attribs as $key => $value) {
			$attribs .= $key . '="' . $value . '" ';
		}
		if ($act->getWorkflowProgressActionURL() != '') {
			print '<a href="' . $act->getWorkflowProgressActionURL() . '&source=dashboard" ' . $attribs . ' class="btn ' . $act->getWorkflowProgressActionStyleClass() . '">' . $act->getWorkflowProgressActionLabel() . '</a> ';
		} else { 
			print '<input type="submit" ' . $attribs . ' name="action_' . $act->getWorkflowProgressActionTask() . '" class="btn ' . $act->getWorkflowProgressActionStyleClass() . '" value="' . $act->getWorkflowProgressActionLabel() . '" /> ';
		}
	 } ?>
	</form>
	</td>
</tr>
<tr class="ccm-workflow-progress-history">
	<td colspan="6">
		<?=Loader::element('workflow/progress/history', array('wp' => $wp))?>
	</td>
</tr>

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