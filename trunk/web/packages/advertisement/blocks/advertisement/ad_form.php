<?
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<br />
<strong>Give this ad a name</strong><br/>
<input type="text" style="width: 90%" value="<?=$ad->name?>" name="name" id="ccm-ad-name" />
<br/><br/>
<strong>Belongs to Group</strong><br />
<fieldset style="margin:6px 0 6px 0;">
<? $groups = $ad->getAllGroups();
	foreach($groups as $g) {
		echo '<label><input '.(in_array($g->agID,$ad->agIDs)?'checked="checked"':'').' type="checkbox" name="adGroupIDs[]" value="'.$g->agID.'"/> '.$g->agName.'</label>';
	}
?>
</fieldset>
Below, specify an image file and a URL, or a block of HTML/JavaScript.
<br>
<fieldset style="margin:6px 0 6px 0;">
<strong>Image File</strong><br />
<?=$al->image('ccm-ad-image', 'fID', 'Choose Image', $bf);?><br /><br />
<strong>Ad URL</strong><br>
<input type="text" name="url" style="width: 90%" value="<?=$ad->url?>" id="ccm-ad-url" /><br /><br />
</fieldset>
<div style="text-align:center">- Or -</div>
<fieldset style="margin:6px 0 6px 0;">
<strong>Ad HTML/JavaScript</strong><br>
<textarea name="html" style="width: 90%; height: 50px" id="ccm-ad-html"><?=$ad->html?></textarea>
</fieldset>
Specify ad target impression/click-thru information.
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:6px;">
<tr>
	<td>Target Impressions<br><input type="text" name="targetImpressions" value="<?=$ad->targetImpressions?>" /></td>
	<td>Target Click-Thrus<br><input type="text" name="targetClickThrus" value="<?=$ad->targetClickThrus?>" /></td>
</tr>
<tr>
	<td colspan="2"><br></td>
</tr>
<tr>
	<td>Current Impressions<br><strong><?=$ad->impressions;?></strong></td>
	<td>Current Click-Thrus<br><strong><?=$ad->clickThrus;?></strong></td>
</tr>
</table>