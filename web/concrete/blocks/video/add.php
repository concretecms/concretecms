<? 
defined('C5_EXECUTE') or die("Access Denied.");

$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<h2><?=t("Video File")?></h2>
<?=$al->video('ccm-b-flv-file', 'fID', t('Choose Video File') );?>

<? $this->inc('form_setup_html.php'); ?> 
