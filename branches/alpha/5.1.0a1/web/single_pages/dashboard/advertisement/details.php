<?
$includeAssetLibrary = true; 

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');
Loader::model('/advertisement/advertisement_details');

$al = Loader::helper('concrete/asset_library');

if ($ad->fID > 0) { 
	$bf = $ad->getFileObject();
}

$assetLibraryPassThru = array(
	'type' => 'image'
);


Loader::element("block_al");
?>
<h1><span>Advertisement Details</span></h1>
<div class="ccm-dashboard-inner">
    <form action="<?=$this->url('/dashboard/advertisement/details','save_details',$ad->aID)?>" method="post" id="update-ads-form">
    <? include(DIR_FILES_BLOCK_TYPES."/advertisement/ad_form.php"); ?>
    <div style="margin-top:6px;">
    <?
    if($ad->aID) {
        echo "<input type=\"hidden\" name=\"aID\" value=\"".$ad->aID."\"/>\n";
        echo $ih->submit('Update','update-ads-form');
    } else { 
        echo $ih->submit('Add', 'update-ads-form');
    } 
    ?>
    <?=$ih->button('Cancel', $this->url('/dashboard/advertisement/'), 'left')?>
    </div>
    <br clear="all" />
    </form>
</div>