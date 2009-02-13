<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<? $chooseMSG = t('Choose File/Image'); ?>
<? $noneMSG = t('None selected.'); ?>

<script type="text/javascript">
var ccm_activeAssetField = "";
ccm_triggerSelectFile = function(fID, af) {
	if (af == null) {
		var af = ccm_activeAssetField;
	}
	var obj = $('#' + af + "-fm-selected");
	var dobj = $('#' + af + "-fm-display");
	dobj.hide();
	obj.show();
	obj.load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/selector_data?fID=' + fID + '&ccm_file_selected_field=' + af);
	var vobj = $('#' + af + "-fm-value");
	vobj.attr('value', fID);
	
	/* 
 	var val = '';
 	val += obj.fileName;

 	$("#" + af + "-display").html(val);
 	$("#" + af + "-value").attr('value',obj.bID); 	
 	
	$("#ccm-al-wrapper").hide();
 	$("#ccm-block-fields").show();
	
	$("#" + af).hide();
	$("#" + af + "-reset").show();*/


 }
 
ccm_clearAsset = function(af) {
	var obj = $('#' + af + "-fm-selected");
	var dobj = $('#' + af + "-fm-display");
	var vobj = $('#' + af + "-fm-value");
	vobj.attr('value', 0);
	obj.hide();
	dobj.show();
}

$(function() {

	$(".ccm-file-manager-launch").click(function() {
		ccm_activeAssetField = $(this).parent().attr('ccm-file-manager-field');
		var filterStr = "";
		$(this).parent().find('.ccm-file-manager-filter').each(function() {
			filterStr += '&' + $(this).attr('name') + '=' + $(this).attr('value');		
		});
		$.fn.dialog.open({
			width: 650,
			height: 450,
			modal: false,
			href: CCM_TOOLS_PATH + "/files/search_dialog?search=1" + filterStr + "<?= (is_object($c)?"&cID=".$c->getCollectionID():"") ?>",
			title: "<?=$chooseMSG?>"
		});
	});
});

</script>