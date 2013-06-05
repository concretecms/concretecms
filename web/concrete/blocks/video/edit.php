<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$bObj=$controller;
	$al = Loader::helper('concrete/asset_library');
	$bf = null;
	if ($controller->getFileID() > 0) { 
		$bf = $controller->getFileObject();
	}
?>
<div class="clearfix">
<label><?=t('Video File')?></label>
<div class="input">
	<?=$al->video('ccm-b-flv-file', 'fID', t('Choose Video File'), $bf);?>
</div>
</div>

<? $this->inc('form_setup_html.php'); ?> 