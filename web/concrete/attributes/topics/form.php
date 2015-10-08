<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-topic-attribute-wrapper">
	<style>
		.tree-view-template_<?php echo $akID?> ul.dynatree-container {
			border: 1px solid #ccc;
		}
	</style>
	<script type="text/javascript">
	$(function() {
		var treeObj = $('.tree-view-template_<?php echo $akID ?>');
		treeObj.ccmtopicstree({
			'treeID': '<?php echo $treeID ?>',
			'treeNodeParentID': '<?php echo $parentNode ?>',
			'chooseNodeInForm': 'multiple',
			'allowFolderSelection': false,
			'selectNodesByKey': [<?php echo $valueIDs ?>],
			'selectMode': '2',
			'noDrag' : true,
			'minExpandLevel': '1',
			'checkbox': true,
			'onSelect' : function(select, node) {
                 if (select) {
                    var element = $('.topics_<?php echo $akID ?> .hidden-value-container input[data-node-id=' + node.data.key + ']');
                    if (!element.length) {
                        $('.topics_<?php echo $akID ?> .hidden-value-container').append('<input data-node-id="' + node.data.key + '" name="topics_<?php echo $akID ?>[]" type="hidden" value="'+node.data.key+'">');
                    }
                 } else {
                    $('.topics_<?php echo $akID ?> input[value='+node.data.key+']').remove();
                 }
             }
		});
	});
	</script>
	<fieldset class="topics_<?php echo $akID ?>">
		<div class="tree-view-template_<?php echo $akID?>">
		</div>
		<div class="dynamic-container">
		</div>
		<div class="hidden-value-container">
		<?php
		if(is_array($valueIDArray)) {
			foreach($valueIDArray as $vID) { ?>
				<input data-node-id="<?=$vID?>" type="hidden" name="topics_<?php echo $akID ?>[]" value="<?php echo $vID ?>">
		<?php }
		} ?>
		</div>
	</fieldset>
</div>
