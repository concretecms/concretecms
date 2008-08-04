<? 
$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2>Video File</h2>
<?=$al->file('ccm-b-flv-file', 'fID', 'Choose Video File');?>

<? include($this->getBlockPath() .'/form_setup_html.php'); ?> 
