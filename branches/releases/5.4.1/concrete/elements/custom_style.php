<?php 
defined('C5_EXECUTE') or die("Access Denied.");
if (ENABLE_CUSTOM_DESIGN == false) {
	die(t('Custom design options have been disabled.'));
}

global $c;

$txt = Loader::helper('text');
$form = Loader::helper('form');
$fh = Loader::helper('form/color'); 
$ah = Loader::helper("concrete/asset_library");

if (isset($_REQUEST['cspID']) && $_REQUEST['cspID'] > 0) {
	$csp = CustomStylePreset::getByID($_REQUEST['cspID']);
	if (is_object($csp)) {
		$style = $csp->getCustomStylePresetRuleObject();
	}
} else if (is_object($style)) {
	$selectedCsrID = $style->getCustomStyleRuleID();
}

if(!$style) $style = new CustomStyleRule();

$cssData = $style->getCustomStyleRuleCustomStylesArray();

$presets = CustomStylePreset::getList();
$presetsArray = array();
foreach($presets as $csp) {
	$presetsArray[$csp->getCustomStylePresetID()] = $csp->getCustomStylePresetName();
}

$presetsArray[0] = t('** Custom (No Preset)');

if (!isset($_REQUEST['csrID'])) {
	$cspID = $style->getCustomStylePresetID();
}

if ($_REQUEST['subtask'] == 'delete_custom_style_preset') {
	$cspID = 0;
}
?>

<?php  if (!$_REQUEST['refresh']) { ?>
<div id="ccm-custom-style-wrapper">
<?php  } ?>

<form method="post" id="ccmCustomCssForm" action="<?php echo $action?>" style="width:96%; margin:auto;">

	<input id="ccm-reset-style" name="reset_css" type="hidden" value="0" />
	
	<?php  if (count($presets) > 0) { ?>
		<h2><?php echo t('Saved Presets')?></h2>
	
		<?php echo $form->select('cspID', $presetsArray, $cspID, array('style' => 'vertical-align: middle'))?>
		<a href="javascript:void(0)" id="ccm-style-delete-preset" style="display: none" onclick="ccmCustomStyle.deletePreset()"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" style="vertical-align: middle" width="16" height="16" border="0" /></a>
		
		<br/><br/>
		
		<input type="hidden" id="ccm-custom-style-refresh-action" value="<?php echo $refreshAction?>" /> 
	<?php  } ?>
	
	<input type="hidden" name="selectedCsrID" value="<?php echo $selectedCsrID?>" />
	<ul id="ccm-styleEditPane-tabs" class="ccm-dialog-tabs" style="margin-bottom:16px; margin-top:4px;">
		<li class="ccm-nav-active"><a id="ccm-styleEditPane-tab-fonts" href="#" onclick="return ccmCustomStyle.tabs(this,'fonts');"><?php echo t('Fonts') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomStyle.tabs(this,'background');"><?php echo t('Background') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomStyle.tabs(this,'border');"><?php echo t('Border') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomStyle.tabs(this,'spacing');"><?php echo t('Spacing') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomStyle.tabs(this,'css');"><?php echo t('CSS')?></a></li> 
	</ul>		
	
	<div id="ccmCustomCssFormTabs">
	
	<div id="ccm-styleEditPane-fonts" class="ccm-styleEditPane">	
		<div>
		<h2><?php  echo t('Fonts')?></h2> 
			<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
					<?php echo t('Face')?>
					</td>
					<td>  
					<select name="font_family"> 
						<option <?php echo ($cssData['font_family']=='inherit')?'selected':'' ?> value="inherit"><?php echo t('Inherit') ?></option>
						<option <?php echo ($cssData['font_family']=='Arial')?'selected':'' ?> value="Arial">Arial</option>
						<option <?php echo ($cssData['font_family']=='Times New Roman')?'selected':'' ?> value="Times New Roman">Times New Roman</option>
						<option <?php echo ($cssData['font_family']=='Courier')?'selected':'' ?> value="Courier">Courier</option>
						<option <?php echo ($cssData['font_family']=='Georgia')?'selected':'' ?> value="Georgia">Georgia</option>
						<option <?php echo ($cssData['font_family']=='Verdana')?'selected':'' ?> value="Verdana">Verdana</option>
					</select>
					</td>
					<td rowspan="99"><div style="width: 30px">&nbsp;</div></td>
					<td> 
					<?php echo t('Size')?> 
					</td>
					<td>
						<input name="font_size" type="text" value="<?php echo htmlentities( $cssData['font_size'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Line Height')?> 
					</td>
					<td>
						<input name="line_height" type="text" value="<?php echo htmlentities( $cssData['line_height'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
					</td>
					<td> 
					<?php echo t('Color')?> 
					</td>
					<td>
					<?php echo $fh->output( 'color', '', $cssData['color']) ?> 
					</td>

				</tr>											
				<tr>
					<td> 
					<?php echo t('Alignment')?> 
					</td>
					<td> 
					<select name="text_align"> 
						<option <?php echo ($cssData['text_align']=='')?'selected':'' ?> value=""><?php echo t('Default')?></option>
						<option <?php echo ($cssData['text_align']=='left')?'selected':'' ?> value="left"><?php echo t('Left')?></option>
						<option <?php echo ($cssData['text_align']=='center')?'selected':'' ?> value="center"><?php echo t('Center')?></option>
						<option <?php echo ($cssData['text_align']=='right')?'selected':'' ?> value="right"><?php echo t('Right')?></option>
						<option <?php echo ($cssData['text_align']=='justify')?'selected':'' ?> value="justify"><?php echo t('Justify')?></option>
					</select>
					</td>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</div> 
	</div>	
	
	<div id="ccm-styleEditPane-background" class="ccm-styleEditPane" style="display:none">
		<div>
		<h2><?php  echo t('Background')?></h2> 
		  <table border="0" cellspacing="0" cellpadding="0" class="ccm-style-property-table">
		  <tr>
		  	<td><?php echo $fh->output( 'background_color', '', $cssData['background_color']) ?></td>
		  	<?php  
		  	$bf = false;
		  	if ($cssData['background_image'] > 0) {
		  		$bf = File::getByID($cssData['background_image']);
		  	} ?>
		  	<td><?php echo $ah->image('background_image', 'background_image', t('Background Image'), $bf)?></td>
		  </tr>
		  <tr>
		  	<td>&nbsp;</td>
		  	<td><strong>Repeat</strong>:<br/>
		  	<input type="radio" value="no-repeat" name="background_repeat" <?php  if ($cssData['background_repeat'] == 'no-repeat' || !$cssData['backgroundImageRepeat']) { ?> checked <?php  } ?> /> None
		  	<input type="radio" value="repeat-x" name="background_repeat" <?php  if ($cssData['background_repeat'] == 'repeat-x') { ?> checked <?php  } ?> /> Horizontal
		  	<input type="radio" value="repeat-y" name="background_repeat" <?php  if ($cssData['background_repeat'] == 'repeat-y') { ?> checked <?php  } ?>/> Vertical
		  	<input type="radio" value="repeat" name="background_repeat" <?php  if ($cssData['background_repeat'] == 'repeat') { ?> checked <?php  } ?>/> All
		  	
		  </table>
		  <div class="ccm-spacer"></div>
		</div>
			
	</div>
	
	
	<div id="ccm-styleEditPane-border" class="ccm-styleEditPane" style="display:none">
	
		<div>
		  <h2><?php  echo t('Border')?></h2>  
			<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td> 
					<?php echo t('Style')?>
					</td>
					<td>
					<select name="border_style" > 
						<option <?php echo ($cssData['border_style']=='none')?'selected':'' ?> value="none"><?php echo t('none')?></option>
						<option <?php echo ($cssData['border_style']=='solid')?'selected':'' ?> value="solid"><?php echo t('solid')?></option>
						<option <?php echo ($cssData['border_style']=='dotted')?'selected':'' ?> value="dotted"><?php echo t('dotted')?></option>
						<option <?php echo ($cssData['border_style']=='dashed')?'selected':'' ?> value="dashed"><?php echo t('dashed')?></option>
						<option <?php echo ($cssData['border_style']=='double')?'selected':'' ?> value="double"><?php echo t('double')?></option>
						<option <?php echo ($cssData['border_style']=='groove')?'selected':'' ?> value="groove"><?php echo t('groove')?></option>
						<option <?php echo ($cssData['border_style']=='inset')?'selected':'' ?> value="inset"><?php echo t('inset')?></option>
						<option <?php echo ($cssData['border_style']=='outset')?'selected':'' ?> value="outset"><?php echo t('outset')?></option>
						<option <?php echo ($cssData['border_style']=='ridge')?'selected':'' ?> value="ridge"><?php echo t('ridge')?></option>
					</select>
					</td>
					<td rowspan="99"><div style="width: 30px">&nbsp;</div></td>

					<td>
						<?php echo t('Width')?>
					</td> 				
					<td>
						<input name="border_width" type="text" value="<?php echo intval($cssData['border_width'])?>" size="2" style="width:20px" /> <span class="ccm-note"><?php echo t('px')?></span>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo t('Direction')?>
					</td>
					<td>
					<select name="border_position" > 
						<option <?php echo ($cssData['border_position']=='full')?'selected':'' ?> value="full"><?php echo t('Full')?></option> 
						<option <?php echo ($cssData['border_position']=='top')?'selected':'' ?> value="top"><?php echo t('Top')?></option> 
						<option <?php echo ($cssData['border_position']=='right')?'selected':'' ?> value="right"><?php echo t('Right')?></option>
						<option <?php echo ($cssData['border_position']=='bottom')?'selected':'' ?> value="bottom"><?php echo t('Bottom')?></option>
						<option <?php echo ($cssData['border_position']=='left')?'selected':'' ?> value="left"><?php echo t('Left')?></option> 
					</select>
					</td>

					<td><?php echo t('Color')?></td>
					<td>
					<?php echo $fh->output( 'border_color', '', $cssData['border_color']) ?> 
					</td> 
				</tr>
			</table>	  
		</div>		

	
	</div>
	
	<div id="ccm-styleEditPane-spacing" class="ccm-styleEditPane" style="display:none">		
		<div>
		<table style="width:100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="width:50%" valign="top">		
		
		  <h2 style="margin-top: 0px"><?php  echo t('Margin ')?></h2>
			<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td> 
					<?php echo t('Top')?>
					</td>
					<td>
					<input name="margin_top" type="text" value="<?php echo htmlentities( $cssData['margin_top'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Right')?>
					</td>
					<td>
					<input name="margin_right" type="text" value="<?php echo htmlentities( $cssData['margin_right'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Bottom')?>&nbsp;&nbsp;
					</td>
					<td>
					<input name="margin_bottom" type="text" value="<?php echo htmlentities( $cssData['margin_bottom'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Left')?>
					</td>
					<td>
					<input name="margin_left" type="text" value="<?php echo htmlentities( $cssData['margin_left'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>												
			</table>	 
		</td><td valign="top">		 
			<h2 style="margin-top: 0px"><?php  echo t('Padding ')?></h2>
			<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td> 
					<?php echo t('Top')?>
					</td>
					<td>
					<input name="padding_top" type="text" value="<?php echo htmlentities( $cssData['padding_top'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Right')?>
					</td>
					<td>
					<input name="padding_right" type="text" value="<?php echo htmlentities( $cssData['padding_right'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Bottom')?>&nbsp;&nbsp;
					</td>
					<td>
					<input name="padding_bottom" type="text" value="<?php echo htmlentities( $cssData['padding_bottom'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Left')?>
					</td>
					<td>
					<input name="padding_left" type="text" value="<?php echo htmlentities( $cssData['padding_left'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>												
			</table>
		
		</tr></table>
		</div>
	</div>		
	
	<div id="ccm-styleEditPane-css" class="ccm-styleEditPane" style="display:none">	 
	
		<div>
		<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td style="width: 50%" valign="top">
		  <h2 style="margin-top: 0px"><?php  echo t('CSS ID')?></h2>  
		  <input name="css_id" type="text" value="<?php echo htmlentities(trim($style->getCustomStyleRuleCSSID()), ENT_COMPAT, APP_CHARSET) ?>" style="width:99%" 
		   onkeyup="ccmCustomStyle.validIdCheck(this,'<?php echo str_replace(array("'",'"'),'',$style->getCustomStyleRuleCSSID()) ?>')" /> 
		  <div id="ccm-styles-invalid-id" class="ccm-error" style="display:none; padding-top:4px;">
		  	<?php echo t('Invalid ID.  This id is currently being used by another element on this page.')?>
		  </div>
		</div>	
		</td>
		<td valign="top" style="width: 50%">
		  <h2 style="margin-top: 0px"><?php  echo t('CSS Class Name(s)')?></h2>  
		  <input name="css_class_name" type="text" value="<?php echo htmlentities(trim($style->getCustomStyleRuleClassName()), ENT_COMPAT, APP_CHARSET) ?>" style="width:99%" />		  		
		</td>
		</tr>
		</table>
		</div>
		
		<div>
		  <h2><?php  echo t('Additional CSS')?></h2> 
		  <textarea name="css_custom" cols="50" rows="4" style="width:99%"><?php echo htmlentities($style->getCustomStyleRuleCSSCustom(), ENT_COMPAT, APP_CHARSET) ?></textarea>		
		</div>	
	</div>
	</div>
	
	<br/>
	
	<?php  if ($cspID > 0) { ?>
	<div id="cspFooterPreset" style="display: none">
		<div class="ccm-note-important">
			<h2><?php echo t('You are changing a preset')?></h2>
			<div><?php echo $form->radio('cspPresetAction', 'update_existing_preset', true)?> <?php echo t('Update "%s" preset everywhere it is used', $csp->getCustomStylePresetName())?></div>
			<div><?php echo $form->radio('cspPresetAction', 'save_as_custom_style')?> <?php echo t('Use this style here, and leave "%s" unchanged', $csp->getCustomStylePresetName())?></div>
			<div><?php echo $form->radio('cspPresetAction', 'create_new_preset')?> <?php echo t('Save this style as a new preset')?><br/><span style="margin-left: 20px"><?php echo $form->text('cspName', array('style' => 'width:  127px', 'disabled' => true))?></span></div>
		</div>
	</div>
	<?php  } ?>
	
	<div id="cspFooterNoPreset" ><?php echo $form->checkbox('cspPresetAction', 'create_new_preset')?> <label for="cspPresetAction" style="display: inline; color: #555"><?php echo t('Save this style as a new preset.')?></label><span style="margin-left: 10px"><?php echo $form->text('cspName', array('style' => 'width:  127px', 'disabled' => true))?></span></div>
	
	<br/>
	
	<div class="ccm-buttons">
	<a href="#" class="ccm-button-left cancel" onclick="jQuery.fn.dialog.closeTop()"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
	
	<a href="javascript:void(0)" onclick="$('#ccmCustomCssForm').submit()" class="ccm-button-right accept"><span><?php echo t('Update')?></span></a>
	<?php  if ($cspID < 1) { ?>
		<a onclick="return ccmCustomStyle.resetAll();" id="ccm-reset-style-button" class="ccm-button-right accept" style="margin-right:8px; "><span><?php echo t('Reset')?></span></a>
	<?php  } ?>
	</div>
	
	<div class="ccm-spacer"></div> 
	
	<div class="ccm-note" style="margin-top:16px;">
		<?php echo t('Note: Styles set here are often overridden by those defined within the various block types.')?>
	</div>		
	
<?php 
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>

<script type="text/javascript">
$(function() {
	ccmCustomStyle.initForm();
});
</script>

<?php  if (!$_REQUEST['refresh']) { ?>
</div>
<?php  } ?>