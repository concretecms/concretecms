<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$parent = TreeNode::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['treeNodeParentID']));
$np = new Permissions($parent);
$tree = $parent->getTreeObject();
if ($tree->getTreeTypeHandle() != 'topic') {
	die;
}

$url = View::url('/dashboard/system/attributes/topics', 'add_topic_node', $parent->getTreeNodeID());
if (is_object($parent) && $np->canAddTopicTreeNode()) { ?>

	<div class="ccm-ui">
		<form method="post" data-topic-form="add-topic-node" class="form-horizontal" action="<?=$url?>">
			<?=Loader::helper('validation/token')->output('add_topic_node')?>
			<div class="control-group">
				<?=$form->label('treeNodeTopicName', t('Topic'))?>
				<div class="controls">
					<?=$form->text('treeNodeTopicName', '', array('class' => 'span4'))?>
				</div>
			</div>

			<div class="dialog-buttons">
				<button class="btn" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
				<button class="btn btn-primary pull-right" type="submit"><?=t('Add')?></button>
			</div>
		</form>
	</div>


<?
}

