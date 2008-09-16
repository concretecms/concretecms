<? 
$includeAssetLibrary = true;
$assetLibraryPassThru = array(
	'type' => 'image'
);

$al = Loader::helper('concrete/asset_library');

$ad = new AdvertisementDetails();

if ($ad->fID > 0) { 
	$bf = $ad->getFileObject();
}

$ad->targetImpressions = 1000;
$ad->targetClickThrus = 1000;
?>
<ul id="ccm-ad-tabs" class="ccm-dialog-tabs">
	<li class=""><a id="ccm-ad-tab-add" href="javascript:void(0);">New Advertisement</a></li>
	<li class="ccm-nav-active"><a id="ccm-ad-tab-existing"  href="javascript:void(0);">Select Existing</a></li>
</ul>

<div id="ccm-adPane-add" class="ccm-adPane" style="display:none">
<? include(dirname(__FILE__) . '/ad_form.php');?>
<input type="hidden" id="ccm-ad-source" name="ad_source" value="existing" />
<input type="hidden" name="clickThrus" value="0" />
<input type="hidden" name="impressions" value="0" />
</div>

<div id="ccm-adPane-existing" class="ccm-adPane">
<h2>Advertisement Source</h2>
<label><input type="radio" name="existing_source" value="single" class="ccm-ad-sourceSelect" <?=($controller->aID?"checked=\"checked\"":"")?> /> Single Advertisement</label>
<label><input type="radio" name="existing_source" value="group" class="ccm-ad-sourceSelect" <?=($controller->agID?"checked=\"checked\"":"")?>/> Group of Advertisements</label>

<div class="ccm-block-field-group" id="ccm-ad-single-source">
<? $existingAds = $ad->Find("TRUE ORDER BY name"); 
if(is_array($existingAds) && count($existingAds)) { ?>
    <h2>Select Advertisement</h2>
    <select name="existing_aID">
    <?
    foreach($existingAds as $a) { ?>
        <option value="<?=$a->aID?>" <?=($controller->aID==$a->aID?"selected=\"selected\"":"");?> ><?=$a->name?></option>
    <? } ?>
    </select>
<? } else { ?>
	There are no existing advertisements
<? } ?>
</div>
<div class="ccm-block-field-group" id="ccm-ad-group-source" style="display:none;">
<h2>Select Group of Advertisements</h2>
<? $groups = $ad->getAllGroups(); 
if(is_array($groups) && count($groups)) { ?>
    <select name="existing_agID">
    <?
    foreach($groups as $g) { ?>
        <option value="<?=$g->agID?>" <?=($controller->agID==$g->agID?"selected=\"selected\"":"");?> ><?=$g->agName?></option>
    <? } ?>
    </select><br /><br />
    <strong>Number of Ads to display:</strong>&nbsp;&nbsp;<input type="text" size="3" value="<?=$controller->numAds?>" name="numAds"/><br />
    (enter 0 to display all in advertisements in group)
<? } else { ?>
	There are no existing advertisement groups
<? } ?>
</div>
</div>