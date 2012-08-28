<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-styleEditPane-spacing" class="ccm-styleEditPane" style="display:none">		
	<div style="clear:both">
		<table style="width:100%" border="0" cellspacing="0" cellpadding="0" class="table"><tr><td style="width:50%" valign="top">		
			<h3><?php echo t('Margin')?></h3>
			<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td> 
						<?=t('Top')?>:
					</td>
					<td>
						<input name="margin_top" type="text" value="<?=htmlentities( $cssData['margin_top'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
						<?=t('Right')?>:
					</td>
					<td>
						<input name="margin_right" type="text" value="<?=htmlentities( $cssData['margin_right'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
						<?=t('Bottom')?>:
					</td>
					<td>
						<input name="margin_bottom" type="text" value="<?=htmlentities( $cssData['margin_bottom'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
						<?=t('Left')?>:
					</td>
					<td>
						<input name="margin_left" type="text" value="<?=htmlentities( $cssData['margin_left'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>												
			</table>	 
		</td>
		<td valign="top">		 
			<h3><?php echo t('Padding')?></h3>
			<table class="ccm-style-property-table" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td> 
						<?=t('Top')?>:
					</td>
					<td>
						<input name="padding_top" type="text" value="<?=htmlentities( $cssData['padding_top'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
						<?=t('Right')?>:
					</td>
					<td>
						<input name="padding_right" type="text" value="<?=htmlentities( $cssData['padding_right'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
						<?=t('Bottom')?>:
					</td>
					<td>
						<input name="padding_bottom" type="text" value="<?=htmlentities( $cssData['padding_bottom'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>
				<tr>
					<td> 
						<?=t('Left')?>:
					</td>
					<td>
						<input name="padding_left" type="text" value="<?=htmlentities( $cssData['padding_left'], ENT_COMPAT, APP_CHARSET) ?>" size=2 style="width:40px" />
					</td>
				</tr>												
			</table>
		</table>
	</div>
</div>		