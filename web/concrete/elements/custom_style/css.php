<?php defined('C5_EXECUTE') or die("Access Denied."); ?>	
<div id="ccm-styleEditPane-css" class="ccm-styleEditPane" style="display:none">	 
	<div style="clear:both">
		<table class="ccm-style-property-table table" border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td style="width: 50%" valign="top">
					<?php echo t('CSS ID')?>:
					<input name="css_id" type="text" value="<?=htmlentities(trim($style->getCustomStyleRuleCSSID()), ENT_COMPAT, APP_CHARSET) ?>" style="width:99%" onkeyup="ccmCustomStyle.validIdCheck(this,'<?=str_replace(array("'",'"'),'',$style->getCustomStyleRuleCSSID()) ?>')" /> 
			 	 	<div id="ccm-styles-invalid-id" class="ccm-error" style="display:none; padding-top:4px;">
						<?=t('Invalid ID.  This id is currently being used by another element on this page.')?>
					</div>
				</td>
				<td valign="top" style="width: 50%">
	 				<?php echo t('CSS Class Name(s)')?>:
	  				<input name="css_class_name" type="text" value="<?=htmlentities(trim($style->getCustomStyleRuleClassName()), ENT_COMPAT, APP_CHARSET) ?>" style="width:99%" />		  		
				</td>
			</tr>
		</table>
	</div>
	<div>
		<?php echo t('Additional CSS')?>:
		<textarea name="css_custom" cols="50" rows="4" style="width:99%"><?=htmlentities($style->getCustomStyleRuleCSSCustom(), ENT_COMPAT, APP_CHARSET) ?></textarea>		
	</div>	
</div>