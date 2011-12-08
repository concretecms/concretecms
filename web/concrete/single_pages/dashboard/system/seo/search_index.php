<?php defined('C5_EXECUTE') or die('Access Denied');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Search Index'), t('Choose which areas on your site are indexed.'), 'span6 offset5', false); ?>
	<form method="post" id="ccm-search-index-manage" action="<?=$this->action('')?>">
		<div class="ccm-pane-body">
			<?php echo $this->controller->token->output('update_search_index');?>
			
			<h3><?=t('Indexing Method')?></h3>
			<? $methods = array(
				'whitelist' => t('Whitelist: Selected areas are only areas indexed.'),
				'blacklist' => t('Blacklist: Every area but the selected areas are indexed.')
			);
			print $form->select('SEARCH_INDEX_AREA_METHOD', $methods, IndexedSearch::getSearchableAreaAction(), array('class'=>'xlarge'));?>
			
			<h3><?=t('Areas')?></h3>

			<? foreach($areas as $a) { ?>
				<div><?=$form->checkbox('arHandle[]', $a, in_array($a, $selectedAreas))?> <?=$a?></div>
			<? } ?>

		</div>
		<div class="ccm-pane-footer">
			<?php
			$ih = Loader::helper('concrete/interface');
			print $ih->submit(t('Save'), 'ccm-search-index-manage', 'right', 'primary');
			?>
		</div>
	</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>