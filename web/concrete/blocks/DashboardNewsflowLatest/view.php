<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? if (is_object($slot)) { ?>
<div>
<?=$slot->getContent()?>
</div>
<? } ?>

<? if ($controller->slot == 'C') { ?>
	<div class="newsflow-paging-next"><a href="javascript:void(0)" onclick="ccm_showNewsflowOffsite(<?=$editionID?>)"><span></span></a></div>

	<script type="text/javascript">
	$(function() {
		ccm_setNewsflowPagingArrowHeight();
	});
	</script>
	
<? } ?>