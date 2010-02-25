<?
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;

$form = Loader::helper('form'); 

//Loader::model('layout'); 




if(intval($_REQUEST['layoutID'])){
	$layout = Layout::getById( intval($_REQUEST['layoutID']) ); 
}else $layout = new Layout( array('type'=>'table','rows'=>1,'columns'=>3 ) ); 

if(!$layout ){ 
	echo t('Error: Layout not found');
	
}else{
	
	$layoutPresets=LayoutPreset::getList();
	
	?>


<form method="post" id="ccmAreaLayoutForm" action="<?=$action?>" style="width:96%; margin:auto;"> 

	<? if (count($layoutPresets) > 0) { ?>
		<h2><?=t('Saved Presets')?></h2>
		
		<select id="lpID" name="lpID">
			<option value="<?=$layoutPreset->lpID?>"><?=t('** Custom (No Preset)') ?></option>
			<? foreach($layoutPresets as $layoutPreset){ ?>
				<option value="<?=$layoutPreset->lpID?>"><?=$layoutPreset->lpName ?></option>
			<? } ?>
		</select>
		<? /*$form->select('lpID', $layoutPresets, $lpID, array('style' => 'vertical-align: middle'))*/ ?>
		<a href="javascript:void(0)" id="ccm-layout-delete-preset" style="display: none" onclick="ccmLayout.deletePreset()"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" style="vertical-align: middle" width="16" height="16" border="0" /></a>
		
		<br/><br/>
		
	<? } ?>
	
	<input name="layoutID" type="hidden" value="<?=intval($layout->layoutID) ?>" />

	<table> 
		<tr>
			<td><?=t('Columns')?></td>
			<td>
				<input name="layout_columns" type="text" value="<?=intval($layout->columns) ?>" size=2 />
			</td>
		</tr>
		<tr>
			<td><?=t('Rows')?></td>
			<td>
				<input name="layout_rows" type="text" value="<?=intval($layout->rows) ?>" size=2 />
			</td>
		</tr>		
	</table>
	
	
	<div style="margin:16px 0px">
		<input name="locked" type="checkbox" value="1" <?= ( intval($layout->locked) ) ? 'checked="checked"' : '' ?> />
		<?= t('Lock Widths') ?>
	</div>
	
	
	<? 
	//To Do: only provide this option if there's 1) blocks in the main area, or 2) existing layouts 
	if( !intval($layout->layoutID) ){ ?>
	<div style="margin:16px 0px"> 
		<?= t('Add layout to: ') ?> 
		<input name="add_to_position" type="radio" value="top" /> <?=t('top') ?>&nbsp; 
		<input name="add_to_position" type="radio" value="bottom" checked="checked" /> <?=t('bottom') ?> 
	</div>
	<? } ?>
	

	<div style="margin-bottom:16px;"> 
		<?=$form->checkbox('cspCreateNew', 1)?> 
		<label for="cspCreateNew" style="display: inline; color: #555"><?=t('Save this style as a new preset.')?></label>
		<span style="margin-left: 10px"><?=$form->text('cspName', array('style' => 'width:  127px', 'disabled' => true))?></span>
	</div>
	
	
	
	<div class="ccm-buttons">
		<a href="#" class="ccm-button-left cancel" onclick="jQuery.fn.dialog.closeTop()"><span><em class="ccm-button-close"><?=t('Cancel')?></em></span></a>
		
		<a href="javascript:void(0)" onclick="$('#ccmAreaLayoutForm').submit()" class="ccm-button-right accept"><span><?=intval($layout->layoutID)?t('Save Changes'):t('Create Layout')?></span></a>
	</div>	 
	

</form>

<? } ?> 