<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php  $chooseMSG = t('Choose File/Image'); ?>
<?php  $noneMSG = t('None selected.'); ?>

<script type="text/javascript">
var ccm_activeAssetField = "";
ccm_chooseAsset = function(obj, activeField) {
	var af = (activeField != null) ? activeField : ccm_activeAssetField;
 	var val = '';
 	val += obj.fileName;

 	$("#" + af + "-display").html(val);
 	$("#" + af + "-value").attr('value',obj.bID); 	
 	
	$("#ccm-al-wrapper").hide();
 	$("#ccm-block-fields").show();
	
	$("#" + af).hide();
	$("#" + af + "-reset").show();

 }
 
ccm_resetAsset = function(id) {
	var id = id.substring(0, id.indexOf('-reset'));
	$("#" + id + '-reset').hide();
	$("#" + id).show();
	$("#" + id + "-display").html('<?php echo $noneMSG?>');
	$("#" + id + "-value").attr('value',0);
}

$(function() {

	$(".ccm-launch-al").click(function() {
		ccm_activeAssetField = $(this).attr('id');
	});
	$(".ccm-reset-al").click(function() {
		ccm_resetAsset($(this).attr('id'));
	});
	$(".ccm-launch-al").dialog({ 
			width: 650,
			height: 450,
			modal: false,
			href: CCM_TOOLS_PATH + "/al.php?launch_in_page=1<?php echo  (is_object($c)?"&cID=".$c->getCollectionID():"") ?>",
			title: "<?php echo $chooseMSG?>"
	});
});

</script>