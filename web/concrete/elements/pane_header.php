<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-header">
<?
	if (!isset($close)) {
		$close = 'ccm_hidePane';
	}
?>

<a class="ccm-button" href="javascript:void(0)" onclick="<?=$close?>()"><span><em class="ccm-button-close"><?=t('Close')?></em></span></a>

</div>