<? 
defined('C5_EXECUTE') or die("Access Denied.");

$bObj=$controller;
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
?>
<div class="clearfix">
<label><?=t('Video File')?></label>
<div class="input">
	<?=$al->video('ccm-b-flv-file', 'fID', t('Choose Video File') );?>
</div>
</div>

<? $this->inc('form_setup_html.php'); ?> 
