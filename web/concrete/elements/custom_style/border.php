<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-styleEditPane-border" class="ccm-styleEditPane" style="display:none">
	<div>
	  <h3><?php echo t('Border')?></h3>  
		<table class="ccm-style-property-table table" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td> 
					<?=t('Style')?>:
				</td>
				<td>
					<select name="border_style" > 
						<option <?=($cssData['border_style']=='none')?'selected':'' ?> value="none"><?=t('none')?></option>
						<option <?=($cssData['border_style']=='solid')?'selected':'' ?> value="solid"><?=t('solid')?></option>
						<option <?=($cssData['border_style']=='dotted')?'selected':'' ?> value="dotted"><?=t('dotted')?></option>
						<option <?=($cssData['border_style']=='dashed')?'selected':'' ?> value="dashed"><?=t('dashed')?></option>
						<option <?=($cssData['border_style']=='double')?'selected':'' ?> value="double"><?=t('double')?></option>
						<option <?=($cssData['border_style']=='groove')?'selected':'' ?> value="groove"><?=t('groove')?></option>
						<option <?=($cssData['border_style']=='inset')?'selected':'' ?> value="inset"><?=t('inset')?></option>
						<option <?=($cssData['border_style']=='outset')?'selected':'' ?> value="outset"><?=t('outset')?></option>
						<option <?=($cssData['border_style']=='ridge')?'selected':'' ?> value="ridge"><?=t('ridge')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?=t('Width')?>:
				</td> 				
				<td>
					<input name="border_width" type="text" value="<?=intval($cssData['border_width'])?>" size="2" style="width:20px" /><span class="ccm-note"> <?=t('px')?></span>
				</td>
			</tr>
			<tr>
				<td>
					<?=t('Direction')?>:
				</td>
				<td>
					<select name="border_position" > 
						<option <?=($cssData['border_position']=='full')?'selected':'' ?> value="full"><?=t('Full')?></option> 
						<option <?=($cssData['border_position']=='top')?'selected':'' ?> value="top"><?=t('Top')?></option> 
						<option <?=($cssData['border_position']=='right')?'selected':'' ?> value="right"><?=t('Right')?></option>
						<option <?=($cssData['border_position']=='bottom')?'selected':'' ?> value="bottom"><?=t('Bottom')?></option>
						<option <?=($cssData['border_position']=='left')?'selected':'' ?> value="left"><?=t('Left')?></option> 
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?=t('Color')?>:
				</td>
				<td>
					<?=$fh->output( 'border_color', '', $cssData['border_color']) ?> 
				</td> 
			</tr>
		</table>	  
	</div>		
</div>