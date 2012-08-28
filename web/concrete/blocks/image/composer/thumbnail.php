<?php defined('C5_EXECUTE') or die("Access Denied.");
if(!isset($width)) {
	$width = 150;
}
if(!isset($height)) {
	$height = 150;
}

$form = Loader::helper('form');
$html = Loader::helper('html');
$url = Loader::helper('concrete/urls');

if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}

$bt = BlockType::getByHandle('image');
$dialogurl = $url->getBlockTypeToolsUrl($bt)."/crop_image";

$al = Loader::helper('concrete/asset_library');

?>
<div>
	<div style="float: left; width: 50%"><?php echo $al->image('ccm-b-image-'.$bID, $this->field('fID'), t('Choose Image'), $bf); ?></div>
	<div style="padding: 0 10px; float: left; margin-left: 10px; ">
		<strong><?=t('Target Width')?></strong>: <?=$width?><br/>
		<strong><?=t('Target Height')?></strong>: <?=$height?><br/>
		<div id="ccm-image-composer-thumbnail-<?php echo $bID ?>" target-width="<?=$width?>" target-height="<?=$height?>" >
		</div>
		<div class="ccm-spacer"></div>
	</div>
	<div class="ccm-spacer" style="height: 10px;"></div>
</div>
<script type="text/javascript">
ccm_triggerSelectFileComplete = function(fID, af) { 
	// af = ccm-b-image-blockid
	var td = $("#ccm-image-composer-thumbnail-" + af.substring(12));
	ccm_alGetFileData(fID, function(data) {
		crop = false;
		td.html('');
		dw = data[0].width;
		dh = data[0].height;
		var tw = $("#ccm-image-composer-thumbnail-" + af.substring(12)).attr("target-width");
		var th = $("#ccm-image-composer-thumbnail-" + af.substring(12)).attr("target-height");
		if (tw != dw) {
			dw = '<span style="color: #f00">' + dw + '</span>';
			crop = true;
		}
		if (th != dh) {
			dh = '<span style="color: #f00">' + dh + '</span>';
			crop = true;
		}
		td.append('<strong><?=t('Actual Width')?></strong>: ' + dw + '<br/>');
		td.append('<strong><?=t('Actual Height')?></strong>: ' + dh + '<br/>');
		td.append('<a href="<?=$dialogurl?>?bID=' + af.substring(12) + '&width=' + tw + '&height=' + th + '&fID=' + fID + '" class="dialog-launch" dialog-modal="false" dialog-width="95%" dialog-height="460" dialog-title="Crop Image" class="dialog-launch" id="cropper-dialog-' + af.substring(12) + '"><?=t('Crop + Upload Image')?></a>');
		$("#cropper-dialog-" + af.substring(12)).dialog();
		if(crop) {
			$("#cropper-dialog-" + af.substring(12)).trigger('click');
		}
	});
	
}

ThumbnailBuilder_onSaveCompleted = function(r) { 
	r = eval('(' + r + ')');
	jQuery.fn.dialog.closeTop();
	ccm_triggerSelectFile(r.fID, 'ccm-b-image-' + r.bID, false);
}

</script>