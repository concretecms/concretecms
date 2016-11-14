<?php defined('C5_EXECUTE') or die('Access Denied');
use \Concrete\Core\Page\Search\IndexedSearch;

?>
	<form method="post" id="ccm-search-index-manage" action="<?=$view->action('')?>">
			<?php echo $this->controller->token->output('update_search_index');?>
			<fieldset>
			<label class="control-label"><?=t('Indexing Method')?></label>
			<div class="form-group">
			<?php $methods = array(
                'whitelist' => t('Whitelist - only use the selected areas below when searching content.'),
                'blacklist' => t('Blacklist - skip the selected areas below when searching content.'),
            );
            echo $form->select('SEARCH_INDEX_AREA_METHOD', $methods, IndexedSearch::getSearchableAreaAction(), array('class' => 'xlarge'));?>
			</div>
			</fieldset>
			<fieldset>
				<label class="control-label"><?=t('Areas')?></label>
			<div class="form-group">

			<?php foreach ($areas as $a) {
    ?>
                <div class="checkbox">
				    <label><?=$form->checkbox('arHandle[]', h($a), in_array($a, $selectedAreas))?> <?=h($a)?></label>
                </div>
			<?php 
} ?>
			</div>
			</fieldset>

		<div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
			<button class="btn btn-danger ccm-button-left" name="reindex" value="1" onclick="return confirm('<?=t('Once the index is clear, you must reindex your site from the Automated Jobs page.')?>')"><?=t('Clear Search Index')?></button>
			<?php
            $ih = Loader::helper('concrete/ui');
            echo $ih->submit(t('Save'), 'ccm-search-index-manage', 'right', 'btn-primary');
            ?>
            </div>
		</div>
	</form>
