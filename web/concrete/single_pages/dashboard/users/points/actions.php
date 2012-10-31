<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper('Community Point Actions', false, false, false, array(), Page::getByPath('/dashboard/users/points', 'ACTIVE'))?>

<?php if($showForm) { ?>
<form method="post" action="<?=$this->action('save')?>" id="ccm-community-points-action" class="form-horizontal">
<div class="ccm-pane-body">
	<?php 
		echo $form->hidden('upaID',$upaID);
	?>

	<div class="control-group">
		<label class="control-label"><?php echo t('Enabled');?></label>
		<div class="controls">
		<label class="checkbox">
			<?=$form->checkbox('upaIsActive', 1, ($upaIsActive == 1 || (!$upaID)))?>
		</label>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Action Handle');?></label>
		<div class="controls">
		<? $args = array();
		if ($upaHasCustomClass) { 
			$args['disabled'] = 'disabled';
		}
		?>
		<?php echo $form->text('upaHandle',$upaHandle, $args);?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Action Name');?></label>
		<div class="controls">
		<?php echo $form->text('upaName',$upaName);?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Default Points');?></label>
		<div class="controls">
		<?php echo $form->text('upaDefaultPoints',$upaDefaultPoints);?>
		</div>
	</div>
	
	<div class="control-group">
		<!--  upaBadgeGroupID -->
		<label class="control-label"><?php echo t('Badge Associated')?></label>
		<div class="controls">
			<?=$form->select('gBadgeID', $badges, $gBadgeID)?>
			<i class="icon-question-sign launch-tooltip" title="<?=t('If a badge is assigned to this action, the first time this user performs this action they will be granted the badge.')?>"></i>
		</div>
	</div>
</div>
<? $label = t('Add');
if ($upaID > 0) {
	$label = t('Update');
}
?>

<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/users/points/actions')?>" class="btn"><?=t('Back to List')?></a>
	<button type="submit" class="ccm-button-right btn primary"><?=$label?> <i class="icon-white icon-ok"></i></button>
</div>
</form>		
<?php } else { ?>
	<div class="ccm-pane-options ccm-pane-options-permanent-search">
		<a href="<?=$this->action('add')?>" class="btn primary"><?=t('Add Action')?></a>
	</div>
	
	<div class="ccm-pane-body">
	<?
		if (!$mode) {
			$mode = $_REQUEST['mode'];
		}
		$txt = Loader::helper('text');
		$keywords = $_REQUEST['keywords'];
		
		if (count($actions) > 0) { ?>	
			<table border="0" cellspacing="0" cellpadding="0" class="ccm-results-list table">
			<tr>
				<th><?=t("Active")?></th>
				<th class="<?=$actionList->getSearchResultsClass('upaName')?>"><a href="<?=$actionList->getSortByURL('upaName', 'asc')?>"><?=t('Action Name')?></a></th>
				<th class="<?=$actionList->getSearchResultsClass('upaHandle')?>"><a href="<?=$actionList->getSortByURL('upaHandle', 'asc')?>"><?=t('Action Handle')?></a></th>
				<th class="<?=$actionList->getSearchResultsClass('upaDefaultPoints')?>"><a href="<?=$actionList->getSortByURL('upaDefaultPoints', 'asc')?>"><?=t('Default Points')?></a></th>
				<th class="<?=$actionList->getSearchResultsClass('upaBadgeGroupID')?>"><a href="<?=$actionList->getSortByURL('upaBadgeGroupID', 'asc')?>"><?=t('Group')?></a></th>
				<th></th>
			</tr>
		<?php 
		foreach($actions as $upa) { 
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			} ?>
			<tr class="">
				<td style="text-align: center"><? if ($upa['upaIsActive']) { ?><i class="icon-ok"></i><? } ?></td>
				<td><?= $upa['upaName']?></td>
				<td><?= $upa['upaHandle']?></td>
				<td><?= number_format($upa['upaDefaultPoints'])?></td>
				<td><?php echo $upa['gName'];?></td>
				<td style="text-align: right">
					<?php echo $concrete_interface->button(t('Edit'),$this->action($upa['upaID']), '', 'btn btn-small')?>

					<?php echo $concrete_interface->button(t('Delete'),$this->action('delete',$upa['upaID']),
						'', 'btn btn-small', array(),"return confirm('<?=t('Are you sure?')?>')"); ?>	

				</td>
			</tr>
		<?php } ?>
		</table>
		<? } else { ?>
			<div id="ccm-list-none"><?=t('No Actions found.')?></div>
		<? } ?>
	</div>
	
<div class="ccm-pane-footer">
<?=$actionList->displayPagingV2(); ?>
</div>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>