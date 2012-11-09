</div>

<?
$c = $b->getBlockCollectionObject();
if (!is_object($a)) {
	$a = Area::get($c, $b->getAreaHandle());
}


$bID = $b->getBlockID();
$arHandle = htmlspecialchars($a->getAreaHandle());
$cID = $c->getCollectionID();
$aID = $a->getAreaID();
?>

<script type="text/javascript">
ccm_onInlineEditCancel = function(onComplete) {
	jQuery.fn.dialog.showLoader();
	var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=<?=$cID?>&bID=<?=$bID?>&arHandle=<?=$arHandle?>&btask=view_edit_mode';	 
	$.get(action, 		
		function(r) { 
			onComplete();
			ccm_inlineEditMode = false;

			$('div.ccm-block-edit-disabled').each(function() {
				$(this).removeClass('ccm-block-edit-disabled');
				$(this).addClass('ccm-block');
			});

			$('div.ccm-add-block').show();

			$('#b<?=$bID?>-<?=$aID?>').before(r).remove();
			ccm_mainNavDisableDirectExit();
			jQuery.fn.dialog.hideLoader();
			if (typeof(onComplete) == 'function') {
			}
		}
	);
}

</script>