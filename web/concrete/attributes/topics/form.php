<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-topic-attribute-wrapper">
	<style>
		.tree-view-template_<?php echo $akID?> ul.dynatree-container {
			border: 1px solid #ccc;
		}
	</style>
	<script type="text/javascript">
	$(function() {
		var treeObj = $('.tree-view-template_<?php echo $akID ?>');
		<?php if(!$valueIDArray) { ?>
			var initialSelect = true; // if this is the first time running, allow initial select. 
		<?php } else {  ?>
			var initialSelect = false;
		<?php } ?>
		treeObj.ccmtopicstree({
			'treeID': '<?php echo $treeID ?>',
			'treeNodeParentID': '<?php echo $parentNode ?>',
			'chooseNodeInForm': 'multiple',
			'selectNodeByKey': '<?php echo $valueIDs ?>',
			'selectMode': '2',
			'noMenu': true,
			'noDrag' : true,
			'minExpandLevel': '1',
			'allChildren': true,
			'checkbox': true,
			'onSelect' : function(select, node) {
				 if(!initialSelect) {  // dynatree insists on creating a value at the beginning to enable this event. we skip that.
				 	initialSelect = true;
				 } else {
	                 if (select) {
	                    $('.topics_<?php echo $akID ?> .hidden-value-container').append('<input name="topics_<?php echo $akID ?>[]" type="hidden" value="'+node.data.key+'">');
	                 } else {
	                    $('.topics_<?php echo $akID ?> input[value='+node.data.key+']').remove();
	                 }
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
				<input type="hidden" name="topics_<?php echo $akID ?>[]" value="<?php echo $vID ?>">
		<?php }
		} ?>
		</div>
	</fieldset>
</div>