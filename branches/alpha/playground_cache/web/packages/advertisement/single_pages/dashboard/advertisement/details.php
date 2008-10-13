<?

defined('C5_EXECUTE') or die(_("Access Denied."));
$includeAssetLibrary = true; 

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');
$bt = BlockType::getByHandle('advertisement');
Loader::model('advertisement_details', 'advertisement');

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
    <? $bt->inc('ad_form.php', array('ad' => $ad, 'bf' => $bf, 'al' => $al)); ?>
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