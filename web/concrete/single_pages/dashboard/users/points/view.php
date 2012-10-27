<?php
print
Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Community Points'), false, false, false, array(
	Page::getByPath('/dashboard/users/points/actions'),
	Page::getByPath('/dashboard/users/points/assign')

))?>
<form class="form-horizontal" action="<?php echo $this->action('view')?>" method="get">
<div class="ccm-pane-options">
<div class="row">
	<div class="span5">
	<label class="control-label">Username</label>
	<div class="controls">
	<?php echo $form_user_selector->quickSelect('uName',$_GET['uName']);?>
	</div>
	</div>
	
	<div class="span3">
		<input type="submit" value="Search" class="btn" />
		<a href="<?=View::url('/dashboard/users/points/assign')?>" class="btn primary">Add New</a>
	</div>
	
</div>
</div>
</form>
<div class="ccm-pane-body ccm-pane-body-footer">

	<?
		if (!$mode) {
			$mode = $_REQUEST['mode'];
		}
		$txt = Loader::helper('text');
		$keywords = $_REQUEST['keywords'];
		
		if (count($entries) > 0) { ?>	
			<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
			<tr>
				<th><a href="<?=$upEntryList->getSortByURL('uName', 'asc')?>"><?=t('User')?></a></th>
				<th><a href="<?=$upEntryList->getSortByURL('upaName', 'asc')?>"><?=t('Action')?></a></th>
				<th><a href="<?=$upEntryList->getSortByURL('upPoints', 'asc')?>"><?=t('Points')?></a></th>
				<th><a href="<?=$upEntryList->getSortByURL('timestamp', 'asc')?>"><?=t('Date Assigned')?></a></th>
				<th></th>
			</tr>
		<?php 
		foreach($entries as $up) { 
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			} ?>
			<tr class="ccm-list-record <?=$striped?>">
				<td><?php echo $up['uName'] ?></td>
				<td>
					<strong><?php echo UserPointAction::getTypeNameFromID($up['upaTypeID']) ?> : <?= $up['upaName']?></strong><br/>
					<div><?php echo nl2br($up['upComments']);?></div>
				</td>
				<td><?php echo number_format($up['upPoints'])?></td>
				<td><?php echo $up['timestamp'];?></td>
				<td>
					<?php echo $concrete_interface->button('Delete',View::url('/dashboard/users/points/','deleteEntry',$up['upID']),
						'right', NULL, array(),"return confirm('Are you sure?')"); ?>
					<?php echo $concrete_interface->button('Edit',View::url('/dashboard/users/points/assign',$up['upID']))?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<? } else { ?>
			<div id="ccm-list-none"><?=t('No Entries found.')?></div>
		<? } 
		$upEntryList->displayPaging(); ?>
</div>
<? print Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>