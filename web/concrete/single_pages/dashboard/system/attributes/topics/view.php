<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Topics'), false, false, false);?>
<div class="ccm-pane-options">
	<select name="topicTreeIDSelect">
		<? foreach($trees as $stree) {?> 
			<option value="<?=$stree->getTreeID()?>" <? if ($tree->getTreeID() == $stree->getTreeID()) { ?>selected<? } ?>><?=$stree->getTreeDisplayName()?></option>
		<? } ?>
	</select>
	<? if (PermissionKey::getByHandle('remove_topic_tree')->validate() && is_object($tree)) { ?>
		<a href="<?=$view->url('/dashboard/system/attributes/topics', 'remove_tree', $tree->getTreeID(), Loader::helper('validation/token')->generate('remove_tree'))?>" onclick="return confirm('<?=t('Are you sure?')?>')" class="btn pull-right btn-danger"><?=t('Delete Topic Tree')?></a>
	<? } ?>
	<? if (PermissionKey::getByHandle('add_topic_tree')->validate()) { ?>
		<a href="<?=$view->url('/dashboard/system/attributes/topics/add')?>" style="margin-right: 10px" class="btn pull-right"><?=t('Add Topic Tree')?></a>
	<? } ?>
</div>
<div class="ccm-pane-body ccm-pane-body-footer">
	<? if (is_object($tree)) { ?>
		<div class="topic-tree" data-topic-tree="<?=$tree->getTreeID()?>">
		</div>

	<script type="text/javascript">
	$(function() {
		$('select[name=topicTreeIDSelect]').on('change', function() {
			window.location.href = '<?=$view->url('/dashboard/system/attributes/topics', 'view')?>' + $(this).val();
		});
		
		$('[data-topic-tree]').ccmtopicstree({
			'treeID': '<?=$tree->getTreeID()?>'
		});
	});
	</script>
<? } ?>

</div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
