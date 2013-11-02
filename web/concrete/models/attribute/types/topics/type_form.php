<script type="text/javascript">
	$(function() {
		$('.tree-view-template').ccmtopicstree({  // run first time around to get default tree if new. 
			'treeID': <?php echo $tree->getTreeID(); ?>,
			'chooseNodeInForm': true,
			'allChildren': true,
			'noDrag' : true,
			//'selectMode': 2,
			<?php if($parentNode) { ?>
             'selectNodeByKey': '<?php echo $parentNode ?>',
             <?php } ?>
			'onSelect' : function(select, node) {
                 if (select) {
                    $('input[name=akTopicParentNodeID]').val(node.data.key);
                 } else {
                    $('input[name=akTopicParentNodeID]').val('');
                 }
             }
		});
		
		var treeViewTemplate = $('.tree-view-template');
		$('select[name=topicTreeIDSelect]').on('change', function() {
			$('input[name="akTopicTreeID"]').val($(this).find(':selected').val());
			$('.tree-view-template').remove();
			$('.tree-view-container').append(treeViewTemplate);
			var toolsURL = '<?php echo Loader::helper('concrete/urls')->getToolsURL('tree/load'); ?>';
			var chosenTree = $(this).val();
			$('.tree-view-template').ccmtopicstree({
				'treeID': chosenTree,
				'chooseNodeInForm': true,
				'onSelect' : function(select, node) {
	                 if (select) {
	                    $('input[name=akTopicParentNodeID]').val(node.data.key);
	                 } else {
	                    $('input[name=akTopicParentNodeID]').val('');
	                 }
	             }
			});
		});
	});
	</script>
<fieldset>
<legend><?=t('Topic Tree')?></legend>
<div class="clearfix"></div>
	<select name="topicTreeIDSelect">
		<? foreach($trees as $stree) { ?> 
			<option value="<?=$stree->getTreeID()?>" <? if ($tree->getTreeID() == $stree->getTreeID()) { ?>selected<? } ?>><?=$stree->getTreeDisplayName()?></option>
		<? } ?>
	</select>
<div class="tree-view-container">
	<div class="tree-view-template">
		<legend><?=t('Topic Default Parent Node')?></legend>
	</div>
</div>
<input type="hidden" name="akTopicParentNodeID" value="<?php echo $parentNode ?>">
<input type="hidden" name="akTopicTreeID" value="<?php echo $tree->getTreeID(); ?>">
</fieldset>
