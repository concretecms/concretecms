<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-styleEditPane-fonts" class="ccm-styleEditPane">	
	<div>
	<h3><?php echo t('Fonts')?></h3>
		<table class="ccm-style-property-table table" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
				<?=t('Face')?>:
				</td>
				<td>  
				<select name="font_family"> 
					<option <?=($cssData['font_family']=='inherit')?'selected':'' ?> value="inherit"><?=t('Inherit') ?></option>
					<option <?=($cssData['font_family']=='Arial')?'selected':'' ?> value="Arial">Arial</option>
					<option <?=($cssData['font_family']=='Times New Roman')?'selected':'' ?> value="Times New Roman">Times New Roman</option>
					<option <?=($cssData['font_family']=='Courier')?'selected':'' ?> value="Courier">Courier</option>
					<option <?=($cssData['font_family']=='Georgia')?'selected':'' ?> value="Georgia">Georgia</option>
					<option <?=($cssData['font_family']=='Verdana')?'selected':'' ?> value="Verdana">Verdana</option>
				</select>
				</td>
			</tr>
			<tr>
				<td> 
				<?=t('Size')?>:
				</td>
				<td>
					<input name="font_size" type="text" value="<?=htmlentities( $cssData['font_size'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
				</td>
			</tr>
			<tr>
				<td> 
					<?=t('Line Height')?>:
				</td>
				<td>
					<input name="line_height" type="text" value="<?=htmlentities( $cssData['line_height'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
				</td>
			</tr>
			<tr>
				<td> 
					<?=t('Color')?>:
				</td>
				<td>
					<?=$fh->output( 'color', '', $cssData['color']) ?> 
				</td>

			</tr>											
			<tr>
				<td> 
				<?=t('Alignment')?>:
				</td>
				<td> 
				<select name="text_align"> 
					<option <?=($cssData['text_align']=='')?'selected':'' ?> value=""><?=t('Default')?></option>
					<option <?=($cssData['text_align']=='left')?'selected':'' ?> value="left"><?=t('Left')?></option>
					<option <?=($cssData['text_align']=='center')?'selected':'' ?> value="center"><?=t('Center')?></option>
					<option <?=($cssData['text_align']=='right')?'selected':'' ?> value="right"><?=t('Right')?></option>
					<option <?=($cssData['text_align']=='justify')?'selected':'' ?> value="justify"><?=t('Justify')?></option>
				</select>
				</td>
			</tr>
		</table>
	</div> 
</div>	