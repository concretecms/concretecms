<? 

defined('C5_EXECUTE') or die(_("Access Denied."));
$includeAssetLibrary = true;
$assetLibraryPassThru = array(
	'type' => 'image'
);
$al = Loader::helper('concrete/asset_library');
?>
<h2><?=t('Image')?></h2>
<?=$al->image('ccm-b-image', 'fID', t('Choose Image') );?>

<br/><br/><br/>
<h2><?=t('Image On-State')?> (<?=t('Optional')?>)</h2>
<?=$al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State') );?>

<br/><br/><br/>
<strong><?=t('Alt Text/Caption')?></strong><br/>
<input type="text" style="width: 200px" name="altText" />