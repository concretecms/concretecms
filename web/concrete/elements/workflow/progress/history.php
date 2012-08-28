<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
$records = WorkflowProgressHistory::getList($wp);
foreach($records as $r) { ?>
	
	<div>
		<strong><?=date(DATE_APP_GENERIC_MDYT_FULL, strtotime($r->getWorkflowProgressHistoryTimestamp()))?></strong>. 
		<?=$r->getWorkflowProgressHistoryDescription();?>
	</div>	
	
<? } ?>