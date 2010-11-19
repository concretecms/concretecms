<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-header">
<?php 
	if (!isset($close)) {
		$close = 'ccm_hidePane';
	}
?>

<a class="ccm-button" href="javascript:void(0)" onclick="<?php echo $close?>()"><span><em class="ccm-button-close"><?php echo t('Close')?></em></span></a>

</div>