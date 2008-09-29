<? 
defined('C5_EXECUTE') or die(_("Access Denied."));
$includeAssetLibrary = true; 
$assetLibraryPassThru = array(
	'type' => 'image'
);
	$al = Loader::helper('concrete/asset_library');

$bf = null;
$bfo = null;

if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}
if ($controller->getFileOnstateID() > 0) { 
	$bfo = $controller->getFileOnstateObject();
}

?>
<h2>Image</h2>
<?=$al->image('ccm-b-image', 'fID', 'Choose Image', $bf);?>

<br/><br/><br/>
<h2>Image On-State (Optional)</h2>
<?=$al->image('ccm-b-image-onstate', 'fOnstateID', 'Choose Image On-State', $bfo);?>

<br/><br/><br/>
<h2>Alt Text/Caption</h2>
<input type="text" style="width: 200px" name="altText" value="<?=$controller->getAltText()?>" />