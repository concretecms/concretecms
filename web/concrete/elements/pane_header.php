<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-header">
<?
	if (!isset($close)) {
		$close = 'jQuery.fn.dialog.closeTop();';
	}
?>

<a class="ccm-button" href="javascript:void(0)" onclick="<?=$close?>()"><span><em class="ccm-button-close"><?=t('Close')?></em></span></a>

</div>
<div style="width: 680px; margin: 0px auto 0px auto">