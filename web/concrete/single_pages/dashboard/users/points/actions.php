<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper('Community Point Actions', false, false, false, array(), Page::getByPath('/dashboard/users/points', 'ACTIVE'))?>

<?php if($showForm) { ?>
<form method="post" action="<?=$this->action('save')?>" id="ccm-community-points-action" class="form-horizontal">
<div class="ccm-pane-body">
	<?php 
		echo $form->hidden('upaID',$upaID);
	?>
	
	<div class="control-group">
		<label class="control-label"><?php echo t('Action Handle');?></label>
		<div class="controls">
		<?php echo $form->text('upaHandle',$upaHandle);?>
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
		<label class="control-label"><?php echo t('Group Associated')?></label>
		<?php echo $form->hidden('upaBadgeGroupID',$upaBadgeGroupID)?>
		<div class="controls">
		<label class="checkbox">
		<span id="upaBadgeGroupName"><?php echo $upaBadgeGroupName?></span>
		<a id="groupSelector" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?mode=groups" dialog-title="<?php echo t('Add Group')?>" dialog-modal="false"><?php echo t('Select Group')?></a>
		</label>
		</div>
	</div>
</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/users/points/actions')?>" class="btn"><?=t('Back to List')?></a>
	<button type="submit" class="ccm-button-right btn primary"><?=t('Add')?> <i class="icon-white icon-ok"></i></button>
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
			<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="table">
			<tr>
				<th><a href="<?=$actionList->getSortByURL('upaName', 'asc')?>"><?=t('Action Name')?></a></th>
				<th><a href="<?=$actionList->getSortByURL('upaHandle', 'asc')?>"><?=t('Action Handle')?></a></th>
				<th><a href="<?=$actionList->getSortByURL('upaDefaultPoints', 'asc')?>"><?=t('Default Points')?></a></th>
				<th><a href="<?=$actionList->getSortByURL('upaBadgeGroupID', 'asc')?>"><?=t('Group')?></a></th>
				<th></th>
			</tr>
		<?php 
		foreach($actions as $upa) { 
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			} ?>
			<tr class="ccm-list-record <?=$striped?>">
				<td><?= $upa['upaName']?></td>
				<td><?= $upa['upaHandle']?></td>
				<td><?= number_format($upa['upaDefaultPoints'])?></td>
				<td><?php echo $upa['gName'];?></td>
				<td>
					<?php echo $concrete_interface->button(t('Delete'),$this->action('delete',$upa['upaID']),
						'right', NULL, array(),"return confirm('<?=t('Are you sure?')?>')"); ?>	
					<?php echo $concrete_interface->button(t('Edit'),$this->action($upa['upaID']))?>
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

<script type="text/javascript">
$(function() {
	$("#groupSelector").dialog();
	ccm_triggerSelectGroup = function(gID, gName) {
		$('#upaBadgeGroupID').val(gID);
		$('#upaBadgeGroupName').text(gName);
		
		//var html = '<input type="checkbox" name="gIDs[]" value="' + gID + '" style="vertical-align: middle" checked="checked" /> ' + gName + '<br/>';
		//$("#ccm-additional-groups").append(html);
	}
});
</script>