<? 
defined('C5_EXECUTE') or die(_("Access Denied."));

$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2><?=t("Video File")?></h2>
<?=$al->file('ccm-b-flv-file', 'fID', t('Choose Video File') );?>

<? include($this->getBlockPath() .'/form_setup_html.php'); ?> 
