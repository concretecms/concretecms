<?
defined('C5_EXECUTE') or die("Access Denied.");
if (ENABLE_AREA_LAYOUTS == false) {
	die(t('Area layouts have been disabled.'));
}
global $c;

$form = Loader::helper('form'); 

//Loader::model('layout'); 


if( intval($_REQUEST['lpID']) ){ 
	$layoutPreset = LayoutPreset::getByID($_REQUEST['lpID']); 
	if(is_object($layoutPreset)){
		$layout = $layoutPreset->getLayoutObject();  
	}
}elseif(intval($_REQUEST['layoutID'])){
	$layout = Layout::getById( intval($_REQUEST['layoutID']) ); 
}else $layout = new Layout( array('type'=>'table','rows'=>1,'columns'=>3 ) ); 

if(!$layout ){ 
	echo t('Error: Layout not found');
	
}else{ 
	
	$layoutPresets=LayoutPreset::getList();
	
	if(intval($layout->lpID)) 
		$layoutPreset = LayoutPreset::getById($layout->lpID); 
	
	?>


<? if (!$_REQUEST['refresh']) { ?>
<div id="ccm-layout-edit-wrapper">
<? } ?>

<style type="text/css">
	#ccmLayoutConfigOptions { margin-top:12px; }
	#ccmLayoutConfigOptions table td { padding-bottom:4px; vertical-align:top; padding-bottom:12px; padding-right:12px; } 
	#ccmLayoutConfigOptions table td.padBottom {  }
</style>

<form method="post" class="ccm-ui" id="ccmAreaLayoutForm" action="<?=$action?>" style="width:96%; margin:auto;"> 

	<input id="ccmAreaLayoutForm_layoutID" name="layoutID" type="hidden" value="<?=intval( $layout->layoutID ) ?>" />  
	<input id="ccmAreaLayoutForm_arHandle" name="arHandle" type="hidden" value="<?=htmlentities( $a->getAreaHandle(), ENT_COMPAT, APP_CHARSET) ?>" /> 

	<? if (count($layoutPresets) > 0) { ?>
		<h2><?=t('Saved Presets')?></h2>
		
		<input type="hidden" id="ccm-layout-refresh-action" value="<?=$refreshAction?>" /> 
		
		<select id="ccmLayoutPresentIdSelector" name="lpID">
			<option value="0"><?=t('** Custom (No Preset)') ?></option>
			<? foreach($layoutPresets as $availablePreset){ ?>
				<option value="<?=$availablePreset->getLayoutPresetID() ?>" <?=($availablePreset->getLayoutPresetID()==intval($layout->lpID))?'selected':''?>><?=$availablePreset->getLayoutPresetName() ?></option>
			<? } ?>
		</select> 
		<a href="javascript:void(0)" id="ccm-layout-delete-preset" style="display: none" onclick="ccmLayoutEdit.deletePreset()"><img src="<?=ASSETS_URL_IMAGES?>/icons/delete_small.png" style="vertical-align: middle" width="16" height="16" border="0" /></a>
		
		<br/><br/>
		
	<? } ?>

	<div id="ccmLayoutConfigOptions">
	
		<table> 
			<tr>
				<td><?=t('Columns')?>:</td>
				<td class="val">
					<input name="layout_columns" type="text" value="<?=intval($layout->columns) ?>" size=2 />
				</td>
				
			</tr>
			<tr>
				<td class="padBottom"><?=t('Rows')?>:</td>
				<td class="val padBottom">
					<input name="layout_rows" type="text" value="<?=intval($layout->rows) ?>" size=2 />
				</td>
			</tr>
			
			<tr>	
				<td class="padBottom"><?=t('Spacing')?>:</td>
				<td class="val padBottom">
					<input name="spacing" type="text" value="<?=intval($layout->spacing) ?>" size=2 /> <?=t('px')?>
				</td>				
			</tr>			
			
			<tr>
				<td class="padBottom"><label for="locked"><?= t('Lock Widths') ?>:</label></td>
				<td class="val padBottom">
					<input id="locked" name="locked" type="checkbox" value="1" <?= ( intval($layout->locked) ) ? 'checked="checked"' : '' ?> />
				</td>				
			</tr>			
							
		</table> 
	
	</div>	
	
	
	<? 
	//To Do: only provide this option if there's 1) blocks in the main area, or 2) existing layouts 
	if( !intval($layout->layoutID) ){ ?>
	<? /*
	<div style="margin:16px 0px"> 
		<?= t('Add layout to: ') ?> 
		<input name="add_to_position" type="radio" value="top" /> <?=t('top') ?>&nbsp; 
		<input name="add_to_position" type="radio" value="bottom" checked="checked" /> <?=t('bottom') ?> 
	</div>
	*/ ?>
	<input type="hidden" name="add_to_position" value="bottom" />
	
	<? } ?>
	
	
	
	
	<? if ( is_object($layoutPreset) ) { ?>
		<div id="layoutPresetActions" style="display: none">
			<label class="radio"><?=$form->radio('layoutPresetAction', 'update_existing_preset', true)?> <?=t('Update "%s" preset everywhere it is used?', $layoutPreset->getLayoutPresetName())?></label>
			<label class="radio"><?=$form->radio('layoutPresetAction', 'save_as_custom')?> <?=t('Use this layout here, and leave "%s" unchanged?', $layoutPreset->getLayoutPresetName())?></label>
			<label class="radio"><?=$form->radio('layoutPresetAction', 'create_new_preset')?> <?=t('Save this style as a new preset?')?><br/><span style="margin-left: 20px"><?=$form->text('layoutPresetNameAlt', array('style' => 'width:  127px', 'disabled' => true))?></span></label>
		</div>
	<? } ?>	

	<div id="layoutPresetActionNew" style="margin-bottom:16px;"> 
		<label for="layoutPresetAction" class="checkbox inline">
			<?=$form->checkbox('layoutPresetAction', 'create_new_preset')?> 
			<?=t('Save this style as a new preset.')?>
		</label>
		<span style="margin-left: 10px"><?=$form->text('layoutPresetName', array('style' => 'width:  127px', 'disabled' => true))?></span>
	</div>
	
	
	
	<? if(!$_REQUEST['refresh']) { ?>
		<div class="ccm-buttons dialog-buttons">
			<a href="#" class="btn ccm-button-left cancel" onclick="jQuery.fn.dialog.closeTop(); return false"><?=t('Cancel')?></a>
			<a href="javascript:void(0)" onclick="$('#ccmAreaLayoutForm').submit()" class="ccm-button-right accept btn primary"><?=intval($layout->layoutID)?t('Save Changes'):t('Add')?></a>
		</div>
	<? } ?>
	

</form>

<script type="text/javascript">
$(function() { ccmLayoutEdit.init(); });
</script>

<? if (!$_REQUEST['refresh']) { ?>
</div>
<? } ?>

<? } ?> 