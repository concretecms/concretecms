<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;?>
<?php 
$bt = BlockType::getByID($b->getBlockTypeID()); 
$txt = Loader::helper('text');
$fh = Loader::helper('form/color'); 

$blockStyles = BlockStyles::retrieve($b->bID,$c);
if(!$blockStyles) $blockStyles = new BlockStyles();
$blockCssData=$blockStyles->getStylesArray();
?>

<form method="post" id="ccmCustomCssForm" action="<?php echo $b->getBlockUpdateCssAction()?>&rcID=<?php echo intval($rcID) ?>" style="width:96%; margin:auto;">

	<input id="ccm-reset-block-css" name="reset_block_css" type="hidden" value="0" />
	
	<ul id="ccm-blockEditPane-tabs" class="ccm-dialog-tabs" style="margin-bottom:16px; margin-top:4px;">
		<li class="ccm-nav-active"><a id="ccm-blockEditPane-tab-fonts" href="#" onclick="return ccmCustomBlockCss.tabs(this,'fonts');"><?php echo t('Fonts') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomBlockCss.tabs(this,'region');"><?php echo t('Region') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomBlockCss.tabs(this,'spacing');"><?php echo t('Spacing') ?></a></li>
		<li><a href="javascript:void(0);" onclick="return ccmCustomBlockCss.tabs(this,'css');"><?php echo t('CSS')?></a></li> 
	</ul>		
	
	<div id="ccm-blockEditPane-fonts" class="ccm-blockEditPane">	
		<div class="ccm-block-field-group"> 
			<h2><?php  echo t('Fonts')?></h2> 
			<table>
				<tr>
					<td>
					<?php echo t('Face')?>
					</td>
					<td>  
					<select name="font_family"> 
						<option <?php echo ($blockCssData['font_family']=='inherit')?'selected':'' ?> value="inherit"><?php echo t('Inherit') ?></option>
						<option <?php echo ($blockCssData['font_family']=='Arial')?'selected':'' ?> value="Arial">Arial</option>
						<option <?php echo ($blockCssData['font_family']=='Times New Roman')?'selected':'' ?> value="Times New Roman">Times New Roman</option>
						<option <?php echo ($blockCssData['font_family']=='Courier')?'selected':'' ?> value="Courier">Courier</option>
						<option <?php echo ($blockCssData['font_family']=='Georgia')?'selected':'' ?> value="Georgia">Georgia</option>
						<option <?php echo ($blockCssData['font_family']=='Verdana')?'selected':'' ?> value="Verdana">Verdana</option>
					</select>
					</td>
				</tr>				
				<tr>
					<td> 
					<?php echo t('Color')?> 
					</td>
					<td>
					<?php echo $fh->output( 'color', '', $blockCssData['color']) ?> 
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Size')?> 
					</td>
					<td>
						<input name="font_size" type="text" value="<?php echo htmlentities( $blockCssData['font_size'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Line Height')?> 
					</td>
					<td>
						<input name="line_height" type="text" value="<?php echo htmlentities( $blockCssData['line_height'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
					</td>
				</tr>	
				<tr>
					<td> 
					<?php echo t('Alignment')?> 
					</td>
					<td> 
					<select name="text_align"> 
						<option <?php echo ($blockCssData['text_align']=='')?'selected':'' ?> value=""><?php echo t('Default')?></option>
						<option <?php echo ($blockCssData['text_align']=='left')?'selected':'' ?> value="left"><?php echo t('Left')?></option>
						<option <?php echo ($blockCssData['text_align']=='center')?'selected':'' ?> value="center"><?php echo t('Center')?></option>
						<option <?php echo ($blockCssData['text_align']=='right')?'selected':'' ?> value="right"><?php echo t('Right')?></option>
						<option <?php echo ($blockCssData['text_align']=='justify')?'selected':'' ?> value="justify"><?php echo t('Justify')?></option>
					</select>
					</td>
				</tr>											
			</table>
		</div> 
	</div>	
	
	<div id="ccm-blockEditPane-region" class="ccm-blockEditPane" style="display:none">
	
		<div class="ccm-block-field-group">
		  <h2><?php  echo t('Background')?></h2> 		
		  <?php echo $fh->output( 'background_color', '', $blockCssData['background_color']) ?> 
		  <div class="ccm-spacer"></div>
		</div>
		
		<div class="ccm-block-field-group">
		  <h2><?php  echo t('Border')?></h2>  
		  <table>  
				<tr>
					<td> 
					<?php echo t('Style')?>
					</td>
					<td>
					<select name="border_style" > 
						<option <?php echo ($blockCssData['border_style']=='none')?'selected':'' ?> value="none"><?php echo t('none')?></option>
						<option <?php echo ($blockCssData['border_style']=='solid')?'selected':'' ?> value="solid"><?php echo t('solid')?></option>
						<option <?php echo ($blockCssData['border_style']=='dotted')?'selected':'' ?> value="dotted"><?php echo t('dotted')?></option>
						<option <?php echo ($blockCssData['border_style']=='dashed')?'selected':'' ?> value="dashed"><?php echo t('dashed')?></option>
						<option <?php echo ($blockCssData['border_style']=='double')?'selected':'' ?> value="double"><?php echo t('double')?></option>
						<option <?php echo ($blockCssData['border_style']=='groove')?'selected':'' ?> value="groove"><?php echo t('groove')?></option>
						<option <?php echo ($blockCssData['border_style']=='inset')?'selected':'' ?> value="inset"><?php echo t('inset')?></option>
						<option <?php echo ($blockCssData['border_style']=='outset')?'selected':'' ?> value="outset"><?php echo t('outset')?></option>
						<option <?php echo ($blockCssData['border_style']=='ridge')?'selected':'' ?> value="ridge"><?php echo t('ridge')?></option>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo t('Color')?>
					</td>
					<td>
					<?php echo $fh->output( 'border_color', '', $blockCssData['border_color']) ?> 
					</td> 
				</tr>
				<tr>
					<td>
						<?php echo t('Width')?>
					</td> 				
					<td>
						<input name="border_width" type="text" value="<?php echo intval($blockCssData['border_width'])?>" size="2" style="width:20px" /> <span class="ccm-note"><?php echo t('px')?></span>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo t('Direction')?>
					</td>
					<td>
					<select name="border_position" > 
						<option <?php echo ($blockCssData['border_position']=='full')?'selected':'' ?> value="full"><?php echo t('Full')?></option> 
						<option <?php echo ($blockCssData['border_position']=='top')?'selected':'' ?> value="top"><?php echo t('Top')?></option> 
						<option <?php echo ($blockCssData['border_position']=='right')?'selected':'' ?> value="right"><?php echo t('Right')?></option>
						<option <?php echo ($blockCssData['border_position']=='bottom')?'selected':'' ?> value="bottom"><?php echo t('Bottom')?></option>
						<option <?php echo ($blockCssData['border_position']=='left')?'selected':'' ?> value="left"><?php echo t('Left')?></option> 
					</select>
					</td>
				</tr>	
			</table>	  
		</div>		
	
	</div>
	
	<div id="ccm-blockEditPane-spacing" class="ccm-blockEditPane" style="display:none">		
		<div class="ccm-block-field-group">
		<table style="width:100%"><tr><td style="width:50%">		
		
		  <h2><?php  echo t('Margin ')?></h2>
			<table>  
				<tr>
					<td> 
					<?php echo t('Top')?>
					</td>
					<td>
					<input name="margin_top" type="text" value="<?php echo htmlentities( $blockCssData['margin_top'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Right')?>
					</td>
					<td>
					<input name="margin_right" type="text" value="<?php echo htmlentities( $blockCssData['margin_right'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Bottom')?>&nbsp;&nbsp;
					</td>
					<td>
					<input name="margin_bottom" type="text" value="<?php echo htmlentities( $blockCssData['margin_bottom'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Left')?>
					</td>
					<td>
					<input name="margin_left" type="text" value="<?php echo htmlentities( $blockCssData['margin_left'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>												
			</table>	 
		</td><td>		 
			<h2><?php  echo t('Padding ')?></h2>
			<table>  
				<tr>
					<td> 
					<?php echo t('Top')?>
					</td>
					<td>
					<input name="padding_top" type="text" value="<?php echo htmlentities( $blockCssData['padding_top'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Right')?>
					</td>
					<td>
					<input name="padding_right" type="text" value="<?php echo htmlentities( $blockCssData['padding_right'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Bottom')?>&nbsp;&nbsp;
					</td>
					<td>
					<input name="padding_bottom" type="text" value="<?php echo htmlentities( $blockCssData['padding_bottom'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
					<?php echo t('Left')?>
					</td>
					<td>
					<input name="padding_left" type="text" value="<?php echo htmlentities( $blockCssData['padding_left'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>												
			</table>
		
		</tr></table>
		</div>
	</div>		
	
	<div id="ccm-blockEditPane-css" class="ccm-blockEditPane" style="display:none">	 
	
		<div class="ccm-block-field-group">
		  <h2><?php  echo t('CSS ID')?></h2>  
		  <input name="css_id" type="text" value="<?php echo htmlentities(trim($blockStyles->getCssID()), ENT_COMPAT, APP_CHARSET) ?>" style="width:99%" 
		   onkeyup="ccmCustomBlockCss.validIdCheck(this,'<?php echo str_replace(array("'",'"'),'',$blockStyles->getCssID()) ?>')" /> 
		  <div id="ccm-block-styles-invalid-id" class="ccm-error" style="display:none; padding-top:4px;">
		  	<?php echo t('Invalid ID.  This id is currently being used by another element on this page.')?>
		  </div>
		</div>	
	
		<div class="ccm-block-field-group">
		  <h2><?php  echo t('CSS Class Name(s)')?></h2>  
		  <input name="css_class_name" type="text" value="<?php echo htmlentities(trim($blockStyles->getClassName()), ENT_COMPAT, APP_CHARSET) ?>" style="width:99%" />		  		
		</div>
		
		<div class="ccm-block-field-group">
		  <h2><?php  echo t('Additional CSS')?></h2> 
		  <textarea name="css_custom" cols="50" rows="4" style="width:99%"><?php echo htmlentities($blockStyles->getCustomCSS(), ENT_COMPAT, APP_CHARSET) ?></textarea>		
		</div>	
	</div>
	
	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
	
	<a href="javascript:$('#ccmCustomCssForm').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Update')?></span></a>
	<a onclick="return ccmCustomBlockCss.resetAll();" class="ccm-button-right accept" style="margin-right:8px; "><span><?php echo t('Reset')?></span></a>
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