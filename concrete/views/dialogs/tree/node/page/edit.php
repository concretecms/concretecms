<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');

    ?>

<div class="ccm-ui">
	<form method="post" data-dialog-form="edit-topic-node" class="form-horizontal" action="<?=$controller->action('update_page_node')?>">
		<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
        <div class="mb-3">
            <label class="form-label"><?=t('Page')?></label>
            <?=Core::make('helper/form/page_selector')->selectPage('pageID', $node->getTreeNodePageID(), [
                'includeSystemPages' => true
            ])?>
        </div>
        <div class="mb-3">
            <label class="form-label"><?=t('Sub-Pages')?></label>
            <div class="form-check">
                <input type="checkbox" id="includeSubpagesInMenu" name="includeSubpagesInMenu" class="form-check-input" value="1" <?php if ($node->includeSubpagesInMenu()) { ?>checked<?php } ?>>
                <label class="form-check-label" for="includeSubpagesInMenu"><?=t('Dynamically include sub-pages in menu.')?></label>
            </div>
        </div>
        <div class="dialog-buttons">
			<button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
			<button class="btn btn-primary float-end" data-dialog-action="submit" type="submit"><?=t('Update')?></button>
		</div>
	</form>

	<script type="text/javascript">
		$(function() {
			ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateTreeNode');
			ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateTreeNode', function(e, data) {
				if (data.form == 'edit-topic-node') {
					ConcreteEvent.publish('ConcreteTreeUpdateTreeNode', {'node': data.response});
				}
			});
		});
	</script>

</div>

